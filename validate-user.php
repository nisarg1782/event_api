<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");


// Database connection
$host = "localhost";

require_once __DIR__ . '/config/db.php';

$conn = db_get_connection();


// Decode JSON input
$input = json_decode(file_get_contents("php://input"), true);
$email = isset($input['email']) ? trim($input['email']) : '';
$contact = isset($input['contact']) ? trim($input['contact']) : '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($contact)) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

// Check if user already registered
$stmt = $conn->prepare("SELECT id FROM event_registration WHERE email = ? OR phone = ?");
$stmt->bind_param("ss", $email, $contact);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        "success" => true,
        "exists" => true,
        "message" => "You are already registered. Payment not required."
    ]);
} else {
    echo json_encode([
        "success" => true,
        "exists" => false,
        "message" => "User not found. You can proceed to register and pay."
    ]);
}

$stmt->close();
$conn->close();
?>
