<?php
function getDatabaseConnection() {
    $host = 'localhost'; // Database host
    $user = 'root'; // Database username
    $password = ''; // Database password
    $dbname = 'arcu_db'; // Database name

    // Create a new mysqli connection
    $con = new mysqli($host, $user, $password, $dbname);

    // Check for connection errors
    if ($con->connect_error) {
        throw new Exception("Connection failed: " . $con->connect_error);
    }

    return $con;
}
?>