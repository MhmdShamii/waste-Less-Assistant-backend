<?php
include __DIR__ . '/../config/cors.php';
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

require "../utils/response.php";

$data = json_decode(file_get_contents("php://input"), true);

$item_id = $data['item_id'] ?? null;

if (!$item_id) {
    jsonResponse(["error" => "Item ID is required"], 400);
    exit();
}

$stmt = $conn->prepare("DELETE FROM items WHERE id=?");
$stmt->bind_param("i", $item_id);

if ($stmt->execute()) {
    jsonResponse(["message" => "Item deleted successfully"]);
} else {
    jsonResponse(["error" => "Failed to delete item: " . $stmt->error]);
}

$stmt->close();
$conn->close();
