<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config/db.php';
$conn = db_get_connection();


$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

// Required fields must match keys sent from React
$required = ['name', 'email', 'phone', 'state_id', 'city_id'];
foreach ($required as $key) {
    if (!isset($data[$key]) || $data[$key] === '') {
        echo json_encode(["success" => false, "message" => "Missing field: $key"]);
        exit;
    }
}

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$state_id = intval($data['state_id']);
$city_id = intval($data['city_id']);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO event_registration (name, email, phone, state_id, city_id)
     VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sssii", $name, $email, $phone, $state_id, $city_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => mysqli_stmt_error($stmt)]);
}

mysqli_stmt_close($stmt);
$conn->close();
