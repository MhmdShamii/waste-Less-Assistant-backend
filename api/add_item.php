<?php
include __DIR__ . '/../config/cors.php';
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

require "../utils/response.php";

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? null;
$name = trim($data['name'] ?? '');
$category_id = $data['category_id'] ?? null;
$expiry_date = trim($data['expiry_date'] ?? '');

if (!$user_id || !$name || !$category_id || !$expiry_date) {
    jsonResponse(["error" => "All fields are required"], 400);
    exit();
}

// Insert into items table
try {
    $stmt = $conn->prepare("INSERT INTO items (user_id, name, category_id, expiry_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $name, $category_id, $expiry_date);

    if ($stmt->execute()) {
        jsonResponse(["message" => "Item added successfully"]);
    } else {
        jsonResponse(["error" => "Failed to add item"], 500);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    jsonResponse(["error" => "Server error: " . $e->getMessage()], 500);
}
?>
