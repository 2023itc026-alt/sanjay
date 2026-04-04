<?php
require_once 'db_config.php';
$name = isset($_GET['name']) ? $_GET['name'] : '';

// Fetch the place and its price
$stmt = $conn->prepare("SELECT name, price FROM explore_places WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if ($place = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'name' => $place['name'],
        'price' => floatval($place['price']) // This must be sent to the JS
    ]);
} else {
    // If it's a static item like "Chennai" that isn't in your DB yet, 
    // you might need a fallback here
    echo json_encode(['success' => false]);
}
?>