<?php
session_start();
require_once 'DB_Connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['acc_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

try {
    $query = "SELECT attendance_date, a.name, a.time, e.eventname 
              FROM attendance a 
              LEFT JOIN events e ON a.event_id = e.id 
              WHERE a.student_id = :student_id 
              ORDER BY a.attendance_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['student_id' => $_SESSION['acc_id']]);

    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($attendance);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch attendance: ' . $e->getMessage()]);
}
?>