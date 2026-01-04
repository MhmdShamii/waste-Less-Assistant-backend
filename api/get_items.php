<?php
include __DIR__ . '/../config/cors.php';
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

require "../utils/response.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? null;

if (!$user_id) {
    jsonResponse(["error" => "User ID is required"], 400);
    exit();
}

// Fetch items with category name using JOIN
$stmt = $conn->prepare("
    SELECT items.id, items.name, items.expiry_date, categories.name AS category
    FROM items
    LEFT JOIN categories ON items.category_id = categories.id
    WHERE items.user_id = ? AND items.status != 'used'
    ORDER BY items.expiry_date ASC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "category" => $row['category'] ?? "Unknown",
        "expiry_date" => $row['expiry_date']
    ];
}

$stmt->close();
$conn->close();

jsonResponse([
    "items" => $items
]);
