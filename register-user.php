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

require_once __DIR__ . '/config/db.php';
// Use centralized DB connection helper
$conn = db_get_connection();

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

$stmt = $conn->prepare(
    "INSERT INTO event_registration (name, email, phone, state_id, city_id, fee, payment_id)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => $conn->error]);
    exit;
}

$stmt->bind_param("sssiiis", $name, $email, $contact, $state_id, $city_id, $fee, $payment_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
