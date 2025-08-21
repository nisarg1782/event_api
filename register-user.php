<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight request
    http_response_code(200);
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "event"; // replace with your DB name

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

// Validate required fields
$required = ['name', 'email', 'contact', 'state_id', 'city_id', 'fee', 'payment_id'];
foreach ($required as $key) {
    if (!isset($data[$key]) || $data[$key] === '') {
        echo json_encode(["success" => false, "message" => "Missing field: $key"]);
        exit;
    }
}

$name = $data['name'];
$email = $data['email'];
$contact = $data['contact'];
$state_id = intval($data['state_id']);
$city_id = intval($data['city_id']);
$fee = intval($data['fee']);
$payment_id = $data['payment_id'];

$stmt = mysqli_prepare($conn, 
    "INSERT INTO event_registration (name, email, phone, state_id, city_id, fee, payment_id)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "sssiiis", 
    $name, $email, $contact, $state_id, $city_id, $fee, $payment_id
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => mysqli_stmt_error($stmt)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
