<?php
require_once 'DB_Connection.php';

header('Content-Type: application/json');

try {
    $query = "SELECT club_id, club_name, description, meeting_schedule, location 
              FROM clubs 
              WHERE status = 'active' 
              ORDER BY club_name ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clubs);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch clubs: ' . $e->getMessage()]);
}
?>