<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['acc_id'])) {
    header("Location: ARCU-Login.php");
    exit();
}

// Fetch user data from the database if needed
// ...

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="icon" href="../img/ARCULOGO.png"/>
    <link rel="stylesheet" href="styles/dashboard.css">
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h4 class="sidebar-heading">Dashboard</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Grades
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Notifications
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <h1 class="h2">Welcome to Your Dashboard</h1>
                <div class="dashboard-content">
                    <!-- Add your dashboard content here -->
                </div>
            </main>
        </div>
    </div>

</body>
</html>