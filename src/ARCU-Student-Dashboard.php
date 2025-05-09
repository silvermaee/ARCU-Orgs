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
                            <a class="nav-link" href="#" data-section="joinClubSection" id="navJoinClub">
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
                                    Upcoming Events
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Check out the latest events happening this week.</p>
                                    <a href="#" class="btn btn-primary">View Events</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    Attendance Records
                                </div>
                                <div class="card-body">
                                    <p class="card-text">View and manage your attendance records.</p>
                                    <a href="#" class="btn btn-primary">View Attendance</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    Join a Club
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Explore and join various clubs and organizations.</p>
                                    <a href="#" class="btn btn-primary">Join Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="joinClubSection" class="section-container d-none" aria-label="Join Club Section">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card mt-4 mb-4">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="card-title mb-0">Join ARCU Club</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="joinClubForm" method="post" novalidate>
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
                                                <button type="submit" class="btn btn-primary">Submit Application</button>
                                            </div>
                                        </form>
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
            const sidebar = document.getElementById('sidebar');

            sidebarToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
            });

            const eventsTab = document.querySelector('.nav-link[href="#"]:nth-child(2)'); // Assuming Events is the second link
            const mainContent = document.querySelector('#content');
            const navJoinClub = document.getElementById('navJoinClub');
            const joinClubSection = document.getElementById('joinClubSection');
            const dashboardSection = document.getElementById('dashboardSection');

            // Handle Clubs section navigation
            navJoinClub.addEventListener('click', function(e) {
                e.preventDefault();
                dashboardSection.classList.add('d-none');
                joinClubSection.classList.remove('d-none');
            });

            eventsTab.addEventListener('click', function (e) {
                e.preventDefault();

                fetch('fetch_events.php')
                    .then(response => response.json())
                    .then(events => {
                        let eventsHTML = '<h2 class="section-title">Your Events</h2>';
                        if (events.length > 0) {
                            eventsHTML += '<div class="row">';
                            events.forEach(event => {
                                eventsHTML += `
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card">
                                            <div class="card-header">${event.title}</div>
                                            <div class="card-body">
                                                <p>${event.description}</p>
                                                <p><strong>Start Date:</strong> ${event.date}</p>
                                                <p><strong>End Date:</strong> ${event.end_date}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            eventsHTML += '</div>';
                        } else {
                            eventsHTML += '<p>No events found.</p>';
                        }

                        mainContent.innerHTML = eventsHTML;
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        mainContent.innerHTML = '<p>Error loading events. Please try again later.</p>';
                    });
            });
        });
    </script>
</body>
</html>