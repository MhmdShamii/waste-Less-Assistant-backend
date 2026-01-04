<?php
include __DIR__ . '/../config/cors.php';
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

require "../utils/response.php";

// Fetch all categories
$result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = [
        "id" => $row['id'],
        "name" => $row['name']
    ];
}

$conn->close();

jsonResponse([
    "categories" => $categories
]);
