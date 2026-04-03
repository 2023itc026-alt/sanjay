<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all trips
    $stmt = $pdo->query("SELECT * FROM trips");
    $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($trips);
} elseif ($method === 'POST') {
    // Add a new trip
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['destination']) && isset($data['date'])) {
        $stmt = $pdo->prepare("INSERT INTO trips (destination, date) VALUES (?, ?)");
        $stmt->execute([$data['destination'], $data['date']]);
        $id = $pdo->lastInsertId();
        echo json_encode(['id' => $id, 'destination' => $data['destination'], 'date' => $data['date']]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>