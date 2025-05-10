<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file setup
$logFile = 'report_generation.log';
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

writeLog("Report generation script started");

// Check if user is logged in
if (!isset($_SESSION['acc_id'])) {
    writeLog("User not logged in");
    header("Location: ARCU-Login.php");
    exit();
}

// Database connection
$host    = 'localhost';
$db      = 'db_arcu';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    writeLog("Database connection successful");
} catch (\PDOException $e) {
    writeLog("Database connection failed: " . $e->getMessage());
    die('Database connection failed: ' . $e->getMessage());
}

if (!file_exists(__DIR__ . '/../tcpdf/tcpdf.php')) {
    die('TCPDF not found at: ' . __DIR__ . '/../tcpdf/tcpdf.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    writeLog("POST request received");
    writeLog("POST data: " . print_r($_POST, true));

    $reportType = $_POST['reportType'] ?? '';
    $dateFrom = $_POST['dateFrom'] ?? '';
    $dateTo = $_POST['dateTo'] ?? '';
    $format = $_POST['reportFormat'] ?? 'pdf';
    
    writeLog("Report Type: $reportType");
    writeLog("Date From: $dateFrom");
    writeLog("Date To: $dateTo");
    writeLog("Format: $format");
    
    // Validate dates
    if (empty($dateFrom) || empty($dateTo)) {
        writeLog("Date validation failed");
        die("Please select both date range fields.");
    }

    try {
        switch ($reportType) {
            case 'events':
                writeLog("Processing events report");
                $status = $_POST['eventStatus'] ?? 'all';
                $query = "SELECT * FROM events WHERE startdate BETWEEN ? AND ?";
                if ($status !== 'all') {
                    $query .= " AND status = ?";
                }
                $query .= " ORDER BY startdate DESC";
                
                writeLog("Query: $query");
                $stmt = $pdo->prepare($query);
                if ($status !== 'all') {
                    $stmt->execute([$dateFrom, $dateTo, $status]);
                } else {
                    $stmt->execute([$dateFrom, $dateTo]);
                }
                break;

            case 'attendance':
                writeLog("Processing attendance report");
                $status = $_POST['attendanceStatus'] ?? 'all';
                $eventId = $_POST['eventFilter'] ?? 'all';
                
                $query = "SELECT a.*, e.eventname, e.startdate 
                         FROM attendance a 
                         JOIN events e ON a.event_id = e.id 
                         WHERE e.startdate BETWEEN ? AND ?";
                
                if ($status !== 'all') {
                    $query .= " AND a.status = ?";
                }
                if ($eventId !== 'all') {
                    $query .= " AND a.event_id = ?";
                }
                $query .= " ORDER BY e.startdate DESC";
                
                writeLog("Query: $query");
                $stmt = $pdo->prepare($query);
                if ($status !== 'all' && $eventId !== 'all') {
                    $stmt->execute([$dateFrom, $dateTo, $status, $eventId]);
                } elseif ($status !== 'all') {
                    $stmt->execute([$dateFrom, $dateTo, $status]);
                } elseif ($eventId !== 'all') {
                    $stmt->execute([$dateFrom, $dateTo, $eventId]);
                } else {
                    $stmt->execute([$dateFrom, $dateTo]);
                }
                break;

            case 'clubs':
                writeLog("Processing clubs report");
                $status = $_POST['memberStatus'] ?? 'all';
                $interest = $_POST['interestFilter'] ?? 'all';
                
                $query = "SELECT * FROM club_members WHERE join_date BETWEEN ? AND ?";
                if ($status !== 'all') {
                    $query .= " AND status = ?";
                }
                if ($interest !== 'all') {
                    $query .= " AND interests LIKE ?";
                }
                $query .= " ORDER BY join_date DESC";
                
                writeLog("Query: $query");
                $stmt = $pdo->prepare($query);
                if ($status !== 'all' && $interest !== 'all') {
                    $interestParam = "%$interest%";
                    $stmt->execute([$dateFrom, $dateTo, $status, $interestParam]);
                } elseif ($status !== 'all') {
                    $stmt->execute([$dateFrom, $dateTo, $status]);
                } elseif ($interest !== 'all') {
                    $interestParam = "%$interest%";
                    $stmt->execute([$dateFrom, $dateTo, $interestParam]);
                } else {
                    $stmt->execute([$dateFrom, $dateTo]);
                }
                break;

            default:
                writeLog("Invalid report type: $reportType");
                throw new Exception("Invalid report type");
        }

        $data = $stmt->fetchAll();
        writeLog("Data fetched: " . count($data) . " records");

        // Generate report based on format
        switch ($format) {
            case 'pdf':
                writeLog("Generating PDF report");
                if (!file_exists('vendor/autoload.php')) {
                    writeLog("TCPDF not found. Please run: composer require tecnickcom/tcpdf");
                    die("TCPDF library not found. Please install it using: composer require tecnickcom/tcpdf");
                }
                require_once(__DIR__ . '/../tcpdf/tcpdf.php');
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // Set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('ARTS AND CULTURE');
                $pdf->SetTitle(ucfirst($reportType) . ' Report');

                // Set margins
                $pdf->SetMargins(15, 15, 15);
                $pdf->SetHeaderMargin(5);
                $pdf->SetFooterMargin(10);

                // Add a page
                $pdf->AddPage();

                // Set font
                $pdf->SetFont('helvetica', '', 12);

                // Add report header
                $pdf->Cell(0, 10, ucfirst($reportType) . ' Report', 0, 1, 'C');
                $pdf->Cell(0, 10, "Period: " . date('F d, Y', strtotime($dateFrom)) . " to " . date('F d, Y', strtotime($dateTo)), 0, 1, 'C');
                $pdf->Ln(10);

                // Add data
                if (!empty($data)) {
                    // Get column headers
                    $headers = array_keys($data[0]);
                    
                    // Calculate column widths
                    $colWidths = [];
                    foreach ($headers as $header) {
                        $colWidths[$header] = 40; // Default width
                    }

                    // Print headers
                    $pdf->SetFont('helvetica', 'B', 12);
                    foreach ($headers as $header) {
                        $pdf->Cell($colWidths[$header], 7, ucwords(str_replace('_', ' ', $header)), 1, 0, 'C');
                    }
                    $pdf->Ln();

                    // Print data
                    $pdf->SetFont('helvetica', '', 12);
                    foreach ($data as $row) {
                        foreach ($headers as $header) {
                            $pdf->Cell($colWidths[$header], 6, $row[$header], 1, 0, 'L');
                        }
                        $pdf->Ln();
                    }
                } else {
                    $pdf->Cell(0, 10, 'No data found for the selected criteria.', 0, 1, 'C');
                }

                writeLog("PDF generation completed");
                // Output PDF
                $pdf->Output(ucfirst($reportType) . '_Report.pdf', 'D');
                break;

            case 'excel':
                writeLog("Generating Excel report");
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . ucfirst($reportType) . '_Report.xls"');
                header('Cache-Control: max-age=0');
                
                echo "Report Type: " . ucfirst($reportType) . "\n";
                echo "Period: " . date('F d, Y', strtotime($dateFrom)) . " to " . date('F d, Y', strtotime($dateTo)) . "\n\n";
                
                if (!empty($data)) {
                    echo implode("\t", array_keys($data[0])) . "\n";
                    foreach ($data as $row) {
                        echo implode("\t", $row) . "\n";
                    }
                }
                writeLog("Excel generation completed");
                break;

            case 'csv':
                writeLog("Generating CSV report");
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment;filename="' . ucfirst($reportType) . '_Report.csv"');
                
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Report Type', ucfirst($reportType)]);
                fputcsv($output, ['Period', date('F d, Y', strtotime($dateFrom)) . " to " . date('F d, Y', strtotime($dateTo))]);
                fputcsv($output, []);
                
                if (!empty($data)) {
                    fputcsv($output, array_keys($data[0]));
                    foreach ($data as $row) {
                        fputcsv($output, $row);
                    }
                }
                fclose($output);
                writeLog("CSV generation completed");
                break;
        }
        writeLog("Report generation completed successfully");
        exit;
    } catch (Exception $e) {
        writeLog("Error generating report: " . $e->getMessage());
        die("Error generating report: " . $e->getMessage());
    }
} else {
    writeLog("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    die("Invalid request method");
}
?> 