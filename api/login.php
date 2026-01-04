<?php
include __DIR__ . '/../config/cors.php';
include __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

require "../utils/response.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonResponse(["error" => "Email and password are required"], 400);
    exit();
}

// Prepare MySQLi statement
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
if (!$stmt) {
    jsonResponse(["error" => "Prepare failed: " . $conn->error], 500);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(["error" => "Invalid email or password"], 401);
}

// Successful login
jsonResponse([
    "message" => "Login successful",
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $user['email']
    ]
]);
