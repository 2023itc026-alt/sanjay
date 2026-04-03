<?php
require_once 'db_config.php';
header('Content-Type: application/json');

$name = isset($_GET['name']) ? $_GET['name'] : '';

$stmt = $conn->prepare("SELECT * FROM explore_places WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'name' => $row['name'],
        'description' => $row['description'],
        'image' => $row['image'],
        'category' => $row['cat']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Place details not found.']);
}
?>