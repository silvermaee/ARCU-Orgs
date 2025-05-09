<?php
require_once 'DB_Connection.php';

header('Content-Type: application/json');

try {
    $query = "SELECT image_id, image_name, image_path, description, upload_date 
              FROM gallery 
              ORDER BY upload_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($images);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch gallery images: ' . $e->getMessage()]);
}
?>