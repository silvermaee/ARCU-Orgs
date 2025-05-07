<?php

// Database connection info - adjust as needed
$host    = 'localhost';
$db      = 'db_arcu';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

// Set up DSN, options
$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Connect to DB
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

$successMessage = '';
$errors         = [];

// Delete Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event'])) {
    $deleteId = (int) ($_POST['event_id'] ?? 0);
    if ($deleteId > 0) {
        $stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
        $stmt->execute([$deleteId]);
        // cascade delete will remove attendance linked automatically
        header('Location: ' . $_SERVER['PHP_SELF'] . '#viewEventsSection');
        exit();
    }
}

// Update Event X
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
    $updateId    = (int) ($_POST['event_id'] ?? 0);
    $eventname   = trim($_POST['eventName'] ?? '');
    $startdate   = $_POST['startDate'] ?? '';
    $enddate     = $_POST['endDate'] ?? '';
    $description = trim($_POST['eventDescription'] ?? '');
    $status      = $_POST['status'] ?? ''; // Assuming 'status' is a field you intend to update

    if ($eventname === '') {
        $errors[] = 'Event Name is required.';
    }
    if (!$startdate) {
        $errors[] = 'Start Date is required.';
    }
    if (!$enddate) {
        $errors[] = 'End Date is required.';
    }
    if ($startdate && $enddate && strtotime($enddate) < strtotime($startdate)) {
        $errors[] = 'End Date cannot be before Start Date.';
    }

    if (empty($errors) && $updateId > 0) {
        $stmt = $pdo->prepare('UPDATE events SET eventname = ?, startdate = ?, enddate = ?, description = ? WHERE id = ?');
        $stmt->execute([$eventname, $startdate, $enddate, $description, $updateId]);
        $successMessage = 'Event updated successfully.';
        header('Location: ' . $_SERVER['PHP_SELF'] . '#viewEventsSection');
        exit();
    }
}

// Handle Create event submission X
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $eventname   = trim($_POST['eventName'] ?? '');
    $startdate   = $_POST['startDate'] ?? '';
    $enddate     = $_POST['endDate'] ?? '';
    $description = trim($_POST['eventDescription'] ?? '');

    if ($eventname === '') {
        $errors[] = 'Event Name is required.';
    }
    if (!$startdate) {
        $errors[] = 'Start Date is required.';
    }
    if (!$enddate) {
        $errors[] = 'End Date is required.';
    }
    if ($startdate && $enddate && strtotime($enddate) < strtotime($startdate)) {
        $errors[] = 'End Date cannot be before Start Date.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO events (eventname, startdate, enddate, description) VALUES (?, ?, ?, ?)');
        $stmt->execute([$eventname, $startdate, $enddate, $description]);
        $successMessage = 'Event created successfully.';
        header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($successMessage) . '#viewEventsSection');
        exit();
    }
}

// Handle Delete attendance request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_attendance'])) {
    $deleteId = (int) ($_POST['attendance_id'] ?? 0);
    if ($deleteId > 0) {
        $stmt = $pdo->prepare('DELETE FROM attendance WHERE id = ?');
        $stmt->execute([$deleteId]);
        header('Location: ' . $_SERVER['PHP_SELF'] . '#attendanceSection');
        exit();
    }
}

// Handle Update attendance request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_attendance'])) {
    $updateId = (int) ($_POST['attendance_id'] ?? 0);
    $name     = trim($_POST['attendeeName'] ?? '');
    $date     = $_POST['attDate'] ?? '';
    $time     = $_POST['attTime'] ?? '';
    $event_id = (int) ($_POST['attEvent'] ?? 0);

    if ($name === '') {
        $errors[] = 'Attendee Name is required.';
    }
    if (!$date) {
        $errors[] = 'Attendance Date is required.';
    }
    if (!$time) {
        $errors[] = 'Attendance Time is required.';
    }
    if ($event_id <= 0) {
        $errors[] = 'Valid Event is required.';
    }

    if (empty($errors) && $updateId > 0) {
        $stmt = $pdo->prepare('UPDATE attendance SET name = ?, date = ?, time = ?, event_id = ? WHERE id = ?');
        $stmt->execute([$name, $date, $time, $event_id, $updateId]);
        $successMessage = 'Attendance updated successfully.';
        header('Location: ' . $_SERVER['PHP_SELF'] . '#attendanceSection');
        exit();
    }
}

// Handle Create attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_attendance'])) {
    $name     = trim($_POST['attendeeName'] ?? '');
    $date     = $_POST['attDate'] ?? '';
    $time     = $_POST['attTime'] ?? '';
    $event_id = (int) ($_POST['attEvent'] ?? 0);

    if ($name === '') {
        $errors[] = 'Attendee Name is required.';
    }
    if (!$date) {
        $errors[] = 'Attendance Date is required.';
    }
    if (!$time) {
        $errors[] = 'Attendance Time is required.';
    }
    if ($event_id <= 0) {
        $errors[] = 'Valid Event is required.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO attendance (name, date, time, event_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $date, $time, $event_id]);
        $successMessage = 'Attendance recorded successfully.';
        header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($successMessage) . '#attendanceSection');
        exit();
    }
}

// Fetch events for display
try {
    $stmt   = $pdo->query('SELECT * FROM events ORDER BY startdate DESC');
    $events = $stmt->fetchAll();
} catch (\PDOException $e) {
    exit('Error fetching events: ' . htmlspecialchars($e->getMessage()));
}

// Fetch attendance for display with event names using JOIN
try {
    $stmt        = $pdo->query('SELECT a.*, e.eventname FROM attendance a JOIN events e ON a.event_id = e.id ORDER BY a.date DESC, a.time DESC');
    $attendances = $stmt->fetchAll();
} catch (\PDOException $e) {
    exit('Error fetching attendance: ' . htmlspecialchars($e->getMessage()));
}

// Calculate event statistics
$totalEvents = count($events);
$now         = new DateTime();
$weekLater   = (new DateTime())->modify('+7 days');
$upcomingCount = 0;
foreach ($events as $event) {
    $eventStart = new DateTime($event['startdate']);
    if ($eventStart >= $now && $eventStart <= $weekLater) {
        $upcomingCount++;
    }
}

// Check for success message from redirect
if (isset($_GET['msg'])) {
    $successMessage = htmlspecialchars($_GET['msg']);
}

// Check edit event or attendance request
$editEvent = null;
if (isset($_GET['edit_id'])) {
    $editId = (int) $_GET['edit_id'];
    $stmt   = $pdo->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$editId]);
    $editEvent = $stmt->fetch();
}

$editAttendance = null;
if (isset($_GET['edit_att_id'])) {
    $editAttId = (int) $_GET['edit_att_id'];
    $stmt      = $pdo->prepare('SELECT * FROM attendance WHERE id = ?');
    $stmt->execute([$editAttId]);
    $editAttendance = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>USG-Officer_Dashboard</title>
        <link rel="icon" href="../img/ARCULOGO.png" />

        <link rel="stylesheet" href="main.css" />
        <link rel="stylesheet" href="../node_modules/bootstrap-icons/font/bootstrap-icons.min.css" />
        <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

        <style>
            body {
                background-color: #f8f9fa;
                overflow-x: hidden;
            }
            .sidebar {
                min-height: calc(100vh - 56px);
                background-color: #343a40;
                color: white;
                transition: all 0.3s;
                position: relative;
            }
            .sidebar.collapsed {
                margin-left: -100%;
            }
            .sidebar-expand-btn {
                position: absolute;
                right: -40px;
                top: 10px;
                background-color: #343a40;
                color: white;
                border: none;
                border-radius: 0 4px 4px 0;
                padding: 8px 12px;
                display: none;
                z-index: 1030;
            }
            .sidebar.collapsed .sidebar-expand-btn {
                display: block;
            }
            .main-content {
                transition: all 0.3s;
                padding: 20px;
            }
            .main-content.expanded {
                margin-left: 0;
                width: 100%;
            }
            .nav-link {
                color: rgba(255, 255, 255, 0.75);
            }
            .nav-link:hover {
                color: white;
            }
            .nav-link.active {
                color: white;
                background-color: rgba(255, 255, 255, 0.1);
            }
            #sidebarToggle {
                cursor: pointer;
            }
            .section-container {
                padding: 20px;
            }
            .status-Scheduled {
                background-color: #17a2b8 !important;
            }
            .status-Ongoing {
                background-color: #28a745 !important;
            }
            .status-Completed {
                background-color: #6c757d !important;
            }
            .status-Cancelled {
                background-color: #dc3545 !important;
            }
            .admin-logo {
                width: 40px;
                height: 40px;
                background-color: #6c757d;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                color: white;
                font-weight: bold;
            }
            .dropdown-menu {
                right: 0;
                left: auto;
            }
            .card {
                margin-bottom: 20px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .card-icon {
                font-size: 2rem;
                color: #0d6efd;
            }
            /* Fix form inline buttons spacing */
            form.inline-form {
                display: inline-block;
                margin: 0;
            }
        </style>
    </head>
    <body>

        <header class="navbar navbar-expand-lg navbar-dark bg-dark" style="background: linear-gradient(140deg, rgb(72, 25, 25) 25%, rgba(10, 10, 10, 1) 60%, rgba(187, 201, 189, 1) 80%);">
            <div class="container-fluid">
                <button id="sidebarToggle" class="btn btn-dark me-2" aria-label="Toggle Sidebar">
                    <i class="bi bi-list"></i>
                </button>

                <div class="d-flex align-items-center">
                    <img src="../img/ARCULOGO.png" alt="Company Logo" height="40" class="me-2" />
                    <a class="navbar-brand" href="#">ARTS AND CULTURE</a>
                </div>

                <div class="dropdown">
                    <div class="d-flex align-items-center text-white" role="button" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2 d-none d-md-inline">Admin Panel</span>
                        <div class="admin-logo" aria-label="Admin Panel Logo">
                            A
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Manage Account</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row">
                <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar" aria-label="Sidebar navigation" style="background: linear-gradient(135deg, #481919 0%, #232526 100%);">
                    <button id="sidebarExpandBtn" class="sidebar-expand-btn" aria-label="Expand Sidebar">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-section="dashboardSection" id="navDashboard">
                                    <i class="bi bi-house"></i>
                                    Home
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="collapse" href="#eventsSubMenu" role="button" aria-expanded="true" aria-controls="eventsSubMenu">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    Events
                                    <i class="bi bi-chevron-down ms-2"></i>
                                </a>

                                <div class="collapse show" id="eventsSubMenu">
                                    <ul class="nav flex-column ps-3">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-section="createEventSection" id="navCreateEvent">
                                                <i class="bi bi-plus-circle me-2"></i>
                                                Create Event
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-section="viewEventsSection" id="navViewEvents">
                                                <i class="bi bi-eye me-2"></i>
                                                View Events
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link active" href="#" data-section="attendanceSection" id="navAttendance">
                                    <i class="bi bi-people"></i>
                                    Attendance
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-cash-coin"></i>
                                    Payments
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-question-diamond"></i>
                                    Lost and Found
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Feedback
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-file-earmark-bar-graph me-2"></i>
                                    Generate Report
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="bi bi-people me-2"></i>
                                    Users
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <main id="content" class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content" role="main">
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <?= $successMessage ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <section id="dashboardSection" class="section-container d-none">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                            <h1 class="h2">Dashboard</h1>
                            <div class="btn-toolbar mb-2 mb-md-0">
                                <div class="btn-group me-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                                    <i class="bi bi-calendar"></i>
                                    This week
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-lg-3">
                                <div class="card" aria-label="Total Events">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="card-title">Total Events</h5>
                                                <h2 class="mb-0" id="totalEventsCount">
                                                    <?= $totalEvents ?>
                                                </h2>
                                            </div>
                                            <div class="card-icon" aria-hidden="true">
                                                <i class="bi bi-calendar-event"></i>
                                            </div>
                                        </div>
                                        <p class="card-text text-muted small mt-2" id="upcomingEventsCount">
                                            <?= $upcomingCount ?>
                                            upcoming this week
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-3">
                                    <div class="card" aria-label="Attendance">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h5 class="card-title">Attendance</h5>
                                                    <h2 class="mb-0">
                                                        <?= count($attendances) ?>
                                                    </h2>
                                                </div>
                                                <div class="card-icon" aria-hidden="true">
                                                    <i class="bi bi-person-check"></i>
                                                </div>
                                            </div>
                                            <p class="card-text text-muted small mt-2">Total attendance records</p>
                                        </div>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </section>

                    <section id="createEventSection" class="section-container d-none" aria-label="Create Event Section">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card mt-4 mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="card-title mb-0">
                                            <?= $editEvent ? 'Edit Event' : 'Create New Event' ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="createEventForm" method="post" novalidate>
                                            <input type="hidden" name="<?= $editEvent ? 'update_event' : 'create_event' ?>" value="1" />
                                            <?php if ($editEvent): ?>
                                                <input type="hidden" name="event_id" value="<?= $editEvent['id'] ?>" />
                                            <?php endif; ?>
                                            <div class="mb-3">
                                                <label for="eventName" class="form-label">Event Name*</label>
                                                <input type="text" class="form-control" id="eventName" name="eventName" required value="<?= htmlspecialchars($_POST['eventName'] ?? $editEvent['eventname'] ?? '') ?>" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="startDate" class="form-label">Start Date*</label>
                                                <input type="date" class="form-control" id="startDate" name="startDate" required value="<?= htmlspecialchars($_POST['startDate'] ?? ($editEvent ? date('Y-m-d', strtotime($editEvent['startdate'])) : '')) ?>" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="endDate" class="form-label">End Date*</label>
                                                <input type="date" class="form-control" id="endDate" name="endDate" required value="<?= htmlspecialchars($_POST['endDate'] ?? ($editEvent ? date('Y-m-d', strtotime($editEvent['enddate'])) : '')) ?>" />
                                            </div>

                                            <div class="mb-3">
                                                <label for="eventDescription" class="form-label">Description</label>
                                                <textarea class="form-control" id="eventDescription" name="eventDescription" rows="4"><?= htmlspecialchars($_POST['eventDescription'] ?? $editEvent['description'] ?? '') ?></textarea>
                                            </div>
                                            <div class="text-end">
                                                <button type="button" class="btn btn-danger me-2" id="cancelCreateEventBtn">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <?= $editEvent ? 'Update Event' : 'Create Event' ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="viewEventsSection" class="section-container d-none" aria-label="View Events Section">
                        <div class="row">
                            <div class="col-12">
                                <div class="card mt-4 mb-4">
                                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">All Events</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="eventsTable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Event Name</th>
                                                        <th scope="col">Start Date</th>
                                                        <th scope="col">End Date</th>
                                                        <th scope="col">Description</th>
                                                        <th scope="col" style="min-width: 110px">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($events)): ?>
                                                        <?php foreach ($events as $index => $event): ?>
                                                            <tr>
                                                                <th scope="row">
                                                                    <?= $index + 1 ?>
                                                                </th>
                                                                <td>
                                                                    <?= htmlspecialchars($event['eventname']) ?>
                                                                </td>
                                                                <td>
                                                                    <?= (new DateTime($event['startdate']))->format('M d, Y ') ?>
                                                                </td>
                                                                <td>
                                                                    <?= (new DateTime($event['enddate']))->format('M d, Y ') ?>
                                                                </td>
                                                                <td>
                                                                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                                                                </td>
                                                                <td>
                                                                    <a
                                                                        href="?edit_id=<?= $event['id'] ?>#createEventSection"
                                                                        class="btn btn-sm btn-outline-secondary"
                                                                        aria-label="Edit Event <?= htmlspecialchars($event['eventname']) ?>"
                                                                        ><i class="bi bi-pencil"></i
                                                                    ></a>
                                                                    <form
                                                                        method="post"
                                                                        class="inline-form"
                                                                        onsubmit="return confirm('Are you sure you want to delete this event? This will also delete related attendance records.');"
                                                                        aria-label="Delete Event <?= htmlspecialchars($event['eventname']) ?>"
                                                                    >
                                                                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>" />
                                                                        <button type="submit" name="delete_event" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">No events found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="attendanceSection" class="section-container" aria-label="Attendance Section">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card mt-4 mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="card-title mb-0">
                                            <?= $editAttendance ? 'Edit Attendance' : 'Record Attendance' ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="attendanceForm" method="post" novalidate>
                                            <input type="hidden" name="<?= $editAttendance ? 'update_attendance' : 'create_attendance' ?>" value="1" />
                                            <?php if ($editAttendance): ?>
                                                <input type="hidden" name="attendance_id" value="<?= $editAttendance['id'] ?>" />
                                            <?php endif; ?>
                                            <div class="mb-3">
                                                <label for="attendeeName" class="form-label">Attendee Name*</label>
                                                <input type="text" class="form-control" id="attendeeName" name="attendeeName" required value="<?= htmlspecialchars($_POST['attendeeName'] ?? $editAttendance['name'] ?? '') ?>" />
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="attDate" class="form-label">Date*</label>
                                                    <input type="date" class="form-control" id="attDate" name="attDate" required value="<?= htmlspecialchars($_POST['attDate'] ?? $editAttendance['date'] ?? '') ?>" />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="attTime" class="form-label">Time*</label>
                                                    <input type="time" class="form-control" id="attTime" name="attTime" required value="<?= htmlspecialchars($_POST['attTime'] ?? $editAttendance['time'] ?? '') ?>" />
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="attEvent" class="form-label">Event*</label>
                                                <select class="form-select" id="attEvent" name="attEvent" required>
                                                    <option value="">Select Event</option>
                                                    <?php
                                                    $selectedEvent = $_POST['attEvent'] ?? $editAttendance['event_id'] ?? '';
                                                    foreach ($events as $event) {
                                                        $selected = ($selectedEvent == $event['id']) ? 'selected' : '';
                                                        echo "<option value=\"{$event['id']}\" $selected>" . htmlspecialchars($event['eventname']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="text-end">
                                                <button type="button" class="btn btn-danger me-2" id="cancelAttendanceBtn">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <?= $editAttendance ? 'Update Attendance' : 'Record Attendance' ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Attendance Records</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="attendanceTable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Attendee Name</th>
                                                        <th scope="col">Date</th>
                                                        <th scope="col">Time</th>
                                                        <th scope="col">Event</th>
                                                        <th scope="col" style="min-width: 110px">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($attendances)): ?>
                                                        <?php foreach ($attendances as $index => $attendance): ?>
                                                            <tr>
                                                                <th scope="row">
                                                                    <?= $index + 1 ?>
                                                                </th>
                                                                <td>
                                                                    <?= htmlspecialchars($attendance['name']) ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($attendance['date']) ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars(date('H:i', strtotime($attendance['time']))) ?>
                                                                </td>
                                                                <td>
                                                                    <?= htmlspecialchars($attendance['eventname']) ?>
                                                                </td>
                                                                <td>
                                                                    <a
                                                                        href="?edit_att_id=<?= $attendance['id'] ?>#attendanceSection"
                                                                        class="btn btn-sm btn-outline-secondary"
                                                                        aria-label="Edit Attendance for <?= htmlspecialchars($attendance['name']) ?>"
                                                                        ><i class="bi bi-pencil"></i
                                                                    ></a>
                                                                    <form
                                                                        method="post"
                                                                        class="inline-form"
                                                                        onsubmit="return confirm('Are you sure you want to delete this attendance record?');"
                                                                        aria-label="Delete Attendance for <?= htmlspecialchars($attendance['name']) ?>"
                                                                    >
                                                                        <input type="hidden" name="attendance_id" value="<?= $attendance['id'] ?>" />
                                                                        <button type="submit" name="delete_attendance" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No attendance records found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sidebarToggle = document.getElementById('sidebarToggle');
                const sidebarExpandBtn = document.getElementById('sidebarExpandBtn');
                const sidebar = document.getElementById('sidebar');
                const content = document.getElementById('content');

                const navLinks = document.querySelectorAll('#sidebar .nav-link[data-section]');
                const sections = document.querySelectorAll('main section.section-container');

                // Toggle sidebar
                function toggleSidebar() {
                    sidebar.classList.toggle('collapsed');
                    content.classList.toggle('expanded');
                    sidebarExpandBtn.style.display = sidebar.classList.contains('collapsed') ? 'block' : 'none';
                }

                // Check window width for initial sidebar state
                function checkWidth() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.add('collapsed');
                        content.classList.add('expanded');
                        sidebarExpandBtn.style.display = 'block';
                    } else {
                        sidebar.classList.remove('collapsed');
                        content.classList.remove('expanded');
                        sidebarExpandBtn.style.display = 'none';
                    }
                }

                // Show section by id and update active links
                function showSection(id) {
                    sections.forEach((section) => section.classList.add('d-none'));
                    const target = document.getElementById(id);
                    if (target) {
                        target.classList.remove('d-none');
                    }
                    navLinks.forEach((link) => {
                        if (link.getAttribute('data-section') === id) {
                            link.classList.add('active');
                        } else {
                            link.classList.remove('active');
                        }
                    });
                }

                // Initial check
                checkWidth();

                // Event listeners
                sidebarToggle.addEventListener('click', toggleSidebar);
                sidebarExpandBtn.addEventListener('click', toggleSidebar);
                window.addEventListener('resize', checkWidth);

                navLinks.forEach((link) => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        showSection(this.getAttribute('data-section'));
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                });

                // Buttons inside event dropdown submenu
                document.querySelectorAll('#eventsSubMenu .nav-link').forEach((link) => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        showSection(link.getAttribute('data-section'));
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                });

                // Special buttons for toggling between create/view events sections
                const cancelCreateEventBtn = document.getElementById('cancelCreateEventBtn');
                const recordNewAttendanceBtn = document.getElementById('recordNewAttendanceBtn');
                const cancelAttendanceBtn = document.getElementById('cancelAttendanceBtn');

                if (cancelCreateEventBtn) {
                    cancelCreateEventBtn.addEventListener('click', function () {
                        showSection('viewEventsSection');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }
                if (recordNewAttendanceBtn) {
                    recordNewAttendanceBtn.addEventListener('click', function () {
                        showSection('attendanceSection');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }
                if (cancelAttendanceBtn) {
                    cancelAttendanceBtn.addEventListener('click', function () {
                        showSection('attendanceSection');
                        // If no edit, reset form
                        if (!<?= $editAttendance ? 'true' : 'false' ?>) {
                            document.getElementById('attendanceForm').reset();
                        }
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }

                // On page load, show based on URL params or default to Attendance
                function showInitialSection() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('edit_id')) {
                        showSection('createEventSection');
                    } else if (urlParams.has('edit_att_id')) {
                        showSection('attendanceSection');
                    } else {
                        const hash = window.location.hash.replace('#', '');
                        if (hash && document.getElementById(hash)) {
                            showSection(hash);
                        } else {
                            showSection('attendanceSection');
                        }
                    }
                }
                showInitialSection();
            });
        </script>
    </body>
</html>