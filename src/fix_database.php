<?php
require_once 'DB_Connection.php';

try {
    $pdo = getDatabaseConnection();
    error_log("Connected to database successfully");

    // Drop the events table if it exists
    $pdo->exec("DROP TABLE IF EXISTS events");
    error_log("Dropped existing events table");

    // Create the events table with the correct structure
    $sql = "CREATE TABLE events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        eventname VARCHAR(255) NOT NULL,
        status SET('Scheduled', 'Ongoing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
        startdate DATE NOT NULL,
        enddate DATE NOT NULL,
        description TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $pdo->exec($sql);
    error_log("Created events table with correct structure");

    // Insert some sample events
    $sql = "INSERT INTO events (eventname, startdate, enddate, description) VALUES 
        ('Sample Event 1', '2024-03-20', '2024-03-21', 'This is a sample event'),
        ('Sample Event 2', '2024-03-25', '2024-03-26', 'This is another sample event')";
    
    $pdo->exec($sql);
    error_log("Inserted sample events");

    echo "Database structure has been fixed successfully!";
} catch (PDOException $e) {
    error_log("Error fixing database: " . $e->getMessage());
    die("Error fixing database: " . $e->getMessage());
}
?>
