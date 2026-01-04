<?php
require "../config/cors.php";
require "../config/db.php";
require "../utils/response.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$name || !$email || !$password) {
    jsonResponse(["error" => "All fields required"], 400);
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
    );
    $stmt->execute([$name, $email, $hashed]);
    jsonResponse(["message" => "Registered successfully"]);
} catch (PDOException $e) {
    jsonResponse(["error" => "Email already exists"], 409);
}
