<?php
session_start();

// Check if user is logged in and is a student
if (!isset($_SESSION['acc_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ARCU-Login.php");
    exit();
}

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
    die('Database connection failed. Please make sure the database is set up correctly. Error: ' . $e->getMessage());
}

$successMessage = '';
$errors         = [];

// Handle Join Club form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_club'])) {
    $studentName = trim($_POST['studentName'] ?? '');
    $studentId = trim($_POST['studentId'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $clubId = (int)($_POST['club_id'] ?? 0);
    $interests = isset($_POST['interests']) ? implode(', ', (array)$_POST['interests']) : '';
    $whyJoin = trim($_POST['whyJoin'] ?? '');

    $errors = [];
    if (empty($studentName)) $errors[] = 'Full Name is required.';
    if (empty($studentId)) $errors[] = 'Student ID is required.';
    if (empty($email)) $errors[] = 'Email Address is required.';
    if ($clubId <= 0) $errors[] = 'Please select a club.';
    if (empty($_POST['interests'])) $errors[] = 'At least one area of interest must be selected.';
    if (empty($whyJoin)) $errors[] = 'Please explain why you want to join.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO club_members (club_id, student_name, student_id, email, phone, interests, why_join, join_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), "pending")');
            $stmt->execute([$clubId, $studentName, $studentId, $email, $phone, $interests, $whyJoin]);
            $successMessage = 'Your application has been submitted successfully!';
            header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($successMessage) . '#clubsSection');
            exit();
        } catch (\PDOException $e) {
            $errors[] = 'Error submitting application: ' . $e->getMessage();
        }
    }
}

// Fetch available clubs
try {
    $stmt = $pdo->query('SELECT * FROM clubs WHERE status = "active" ORDER BY club_name ASC');
    $clubs = $stmt->fetchAll();
} catch (\PDOException $e) {
    error_log("Error fetching clubs: " . $e->getMessage());
    $clubs = [];
}

// Check for success message from redirect
if (isset($_GET['msg'])) {
    $successMessage = htmlspecialchars($_GET['msg']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="main.css" />
    <link rel="stylesheet" href="../node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        .navbar {
            background: linear-gradient(140deg, #4e342e, #3e2723);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        .sidebar {
            background-color: #3e2723;
            color: white;
            min-height: 100vh;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            transition: color 0.3s, background-color 0.3s;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background-color: #5d4037;
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .btn {
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #6a1b9a;
            border-color: #6a1b9a;
        }
        .btn-primary:hover {
            background-color: #4a148c;
            border-color: #4a148c;
        }
        .section-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #3e2723;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #6c757d;
            color: white;
        }
        .table td {
            background-color: #f8f9fa;
        }
        /* Responsive sidebar styles */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                width: 250px;
                height: 100%;
                transition: left 0.3s ease;
                z-index: 1050;
            }
            .sidebar.collapsed {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                transition: margin-left 0.3s ease;
            }
            .sidebar.collapsed + .main-content {
                margin-left: 250px;
            }
        }
    </style>
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-dark">
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
                    <span class="me-2 d-none d-md-inline">Student Panel</span>
                    <div class="admin-logo" aria-label="Student Panel Logo">
                        S
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Manage Account</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="ARCU-Logout.php"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="bi bi-house"></i>
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-calendar-event"></i>
                                Events
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-people"></i>
                                Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-images"></i>
                                Gallery
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-collection"></i>
                                Clubs
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main id="content" class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <section id="dashboardSection" class="section-container">
                    <h2 class="section-title">Welcome to Your Dashboard</h2>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-calendar-event me-2"></i>Upcoming Events
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Check out the latest events happening this week.</p>
                                    <a href="#" class="btn btn-primary" id="viewEventsBtn">
                                        <i class="bi bi-eye me-2"></i>View Events
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-person-check me-2"></i>Attendance Records
                                </div>
                                <div class="card-body">
                                    <p class="card-text">View and manage your attendance records.</p>
                                    <a href="#" class="btn btn-primary" id="viewAttendanceBtn">
                                        <i class="bi bi-eye me-2"></i>View Attendance
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-people me-2"></i>Join a Club
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Explore and join various clubs and organizations.</p>
                                    <a href="#" class="btn btn-primary" id="viewClubsBtn">
                                        <i class="bi bi-eye me-2"></i>View Clubs
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Events Section -->
                    <div id="eventsSection" class="mt-5" style="display: none;">
                        <h2 class="section-title">Upcoming Events</h2>
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="error-message"></div>
                        <div id="eventsContainer" class="row"></div>
                    </div>

                    <!-- Attendance Section -->
                    <div id="attendanceSection" class="mt-5" style="display: none;">
                        <h2 class="section-title">Attendance Records</h2>
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="error-message"></div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Event</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceContainer"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Clubs Section -->
                    <div id="clubsSection" class="mt-5" style="display: none;">
                        <h2 class="section-title">Available Clubs</h2>
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="error-message"></div>
                        <div id="clubsContainer" class="row"></div>

                        <!-- Join Club Form -->
                        <div class="card mt-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">Join ARCU Club</h5>
                            </div>
                            <div class="card-body">
                                <form id="joinClubForm" method="post" novalidate>
                                    <div class="mb-3">
                                        <label for="club_id" class="form-label">Select Club*</label>
                                        <select class="form-select" id="club_id" name="club_id" required>
                                            <option value="">Choose a club...</option>
                                            <?php foreach ($clubs as $club): ?>
                                                <option value="<?= $club['club_id'] ?>"><?= htmlspecialchars($club['club_name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="studentName" class="form-label">Full Name*</label>
                                        <input type="text" class="form-control" id="studentName" name="studentName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="studentId" class="form-label">Student ID*</label>
                                        <input type="text" class="form-control" id="studentId" name="studentId" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address*</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                    <div class="mb-3">
                                        <label for="interests" class="form-label">Areas of Interest*</label>
                                        <select class="form-select" id="interests" name="interests" multiple required>
                                            <option value="visual_arts">Visual Arts</option>
                                            <option value="performing_arts">Performing Arts</option>
                                            <option value="music">Music</option>
                                            <option value="dance">Dance</option>
                                            <option value="literature">Literature</option>
                                            <option value="cultural_events">Cultural Events</option>
                                        </select>
                                        <div class="form-text">Hold Ctrl/Cmd to select multiple options</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="whyJoin" class="form-label">Why do you want to join?*</label>
                                        <textarea class="form-control" id="whyJoin" name="whyJoin" rows="3" required></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" name="join_club" class="btn btn-primary">Submit Application</button>
                                    </div>
                                </form>
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
            const sidebar = document.getElementById('sidebar');
            const viewEventsBtn = document.getElementById('viewEventsBtn');
            const viewAttendanceBtn = document.getElementById('viewAttendanceBtn');
            const viewClubsBtn = document.getElementById('viewClubsBtn');

            // Toggle sidebar
            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
            });

            // Function to show section and hide others
            function showSection(sectionId) {
                const sections = ['eventsSection', 'attendanceSection', 'clubsSection'];
                sections.forEach(id => {
                    document.getElementById(id).style.display = id === sectionId ? 'block' : 'none';
                });
            }

            // Function to fetch and display events
            function fetchAndDisplayEvents() {
                const loadingSpinner = document.querySelector('#eventsSection .loading-spinner');
                const errorMessage = document.querySelector('#eventsSection .error-message');
                const eventsContainer = document.getElementById('eventsContainer');

                loadingSpinner.style.display = 'block';
                errorMessage.style.display = 'none';
                eventsContainer.innerHTML = '';

                fetch('fetch_events.php')
                    .then(response => response.json())
                    .then(events => {
                        loadingSpinner.style.display = 'none';
                        if (events.error) {
                            errorMessage.textContent = events.error;
                            errorMessage.style.display = 'block';
                            return;
                        }

                        if (events.length > 0) {
                            events.forEach(event => {
                                const eventDate = new Date(event.date);
                                const endDate = new Date(event.end_date);
                                const formattedStartDate = eventDate.toLocaleDateString('en-US', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });
                                const formattedEndDate = endDate.toLocaleDateString('en-US', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });

                                const eventCard = `
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card event-card">
                                            <div class="card-header">
                                                ${event.title}
                                            </div>
                                            <div class="card-body">
                                                <div class="event-date">
                                                    <i class="bi bi-calendar me-2"></i>${formattedStartDate}
                                                </div>
                                                <div class="event-date">
                                                    <i class="bi bi-calendar-check me-2"></i>${formattedEndDate}
                                                </div>
                                                <p class="event-description">${event.description}</p>
                                                <button class="btn btn-primary w-100">
                                                    <i class="bi bi-info-circle me-2"></i>View Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                eventsContainer.innerHTML += eventCard;
                            });
                        } else {
                            eventsContainer.innerHTML = '<div class="col-12 text-center"><p>No events found.</p></div>';
                        }
                    })
                    .catch(error => {
                        loadingSpinner.style.display = 'none';
                        errorMessage.textContent = 'Error loading events. Please try again later.';
                        errorMessage.style.display = 'block';
                        console.error('Error fetching events:', error);
                    });
            }

            // Function to fetch and display attendance
            function fetchAndDisplayAttendance() {
                const loadingSpinner = document.querySelector('#attendanceSection .loading-spinner');
                const errorMessage = document.querySelector('#attendanceSection .error-message');
                const attendanceContainer = document.getElementById('attendanceContainer');

                loadingSpinner.style.display = 'block';
                errorMessage.style.display = 'none';
                attendanceContainer.innerHTML = '';

                fetch('fetch_attendance.php')
                    .then(response => response.json())
                    .then(attendance => {
                        loadingSpinner.style.display = 'none';
                        if (attendance.error) {
                            errorMessage.textContent = attendance.error;
                            errorMessage.style.display = 'block';
                            return;
                        }

                        if (attendance.length > 0) {
                            attendance.forEach(record => {
                                const date = new Date(record.attendance_date);
                                const formattedDate = date.toLocaleDateString('en-US', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });

                                const statusClass = record.status === 'present' ? 'text-success' : 
                                                 record.status === 'absent' ? 'text-danger' : 'text-warning';

                                const row = `
                                    <tr>
                                        <td>${formattedDate}</td>
                                        <td>${record.eventname || 'Regular Attendance'}</td>
                                        <td><span class="${statusClass}">${record.status}</span></td>
                                    </tr>
                                `;
                                attendanceContainer.innerHTML += row;
                            });
                        } else {
                            attendanceContainer.innerHTML = '<tr><td colspan="3" class="text-center">No attendance records found.</td></tr>';
                        }
                    })
                    .catch(error => {
                        loadingSpinner.style.display = 'none';
                        errorMessage.textContent = 'Error loading attendance records. Please try again later.';
                        errorMessage.style.display = 'block';
                        console.error('Error fetching attendance:', error);
                    });
            }

            // Function to fetch and display clubs
            function fetchAndDisplayClubs() {
                const loadingSpinner = document.querySelector('#clubsSection .loading-spinner');
                const errorMessage = document.querySelector('#clubsSection .error-message');
                const clubsContainer = document.getElementById('clubsContainer');

                loadingSpinner.style.display = 'block';
                errorMessage.style.display = 'none';
                clubsContainer.innerHTML = '';

                fetch('fetch_clubs.php')
                    .then(response => response.json())
                    .then(clubs => {
                        loadingSpinner.style.display = 'none';
                        if (clubs.error) {
                            errorMessage.textContent = clubs.error;
                            errorMessage.style.display = 'block';
                            return;
                        }

                        if (clubs.length > 0) {
                            clubs.forEach(club => {
                                const clubCard = `
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card club-card">
                                            <div class="card-header">
                                                ${club.club_name}
                                            </div>
                                            <div class="card-body">
                                                <p class="club-description">${club.description}</p>
                                                <div class="club-details">
                                                    <p><i class="bi bi-clock me-2"></i>${club.meeting_schedule}</p>
                                                    <p><i class="bi bi-geo-alt me-2"></i>${club.location}</p>
                                                </div>
                                                <button class="btn btn-primary w-100" onclick="joinClub(${club.club_id})">
                                                    <i class="bi bi-plus-circle me-2"></i>Join Club
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                clubsContainer.innerHTML += clubCard;
                            });
                        } else {
                            clubsContainer.innerHTML = '<div class="col-12 text-center"><p>No clubs found.</p></div>';
                        }
                    })
                    .catch(error => {
                        loadingSpinner.style.display = 'none';
                        errorMessage.textContent = 'Error loading clubs. Please try again later.';
                        errorMessage.style.display = 'block';
                        console.error('Error fetching clubs:', error);
                    });
            }

            // Event listeners for sidebar navigation
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    // Show appropriate section based on clicked link
                    if (this.querySelector('.bi-calendar-event')) {
                        showSection('eventsSection');
                        fetchAndDisplayEvents();
                    } else if (this.querySelector('.bi-person-check')) {
                        showSection('attendanceSection');
                        fetchAndDisplayAttendance();
                    } else if (this.querySelector('.bi-collection')) {
                        showSection('clubsSection');
                        fetchAndDisplayClubs();
                    }
                });
            });

            // Event listeners for dashboard buttons
            viewEventsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showSection('eventsSection');
                fetchAndDisplayEvents();
            });

            viewAttendanceBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showSection('attendanceSection');
                fetchAndDisplayAttendance();
            });

            viewClubsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showSection('clubsSection');
                fetchAndDisplayClubs();
            });

            // Initial load of events
            fetchAndDisplayEvents();
        });

        // Function to handle joining a club
        function joinClub(clubId) {
            // Add your join club logic here
            alert('Join club functionality will be implemented soon!');
        }
    </script>
</body>
</html>