<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Database connection
include 'db.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

// Prepare data safely
$name = $conn->real_escape_string($input['name'] ?? '');
$email = $conn->real_escape_string($input['email'] ?? '');
$contact = $conn->real_escape_string($input['contact'] ?? '');
$state_id = isset($input['state_id']) ? (int)$input['state_id'] : NULL;
$city_id = isset($input['city_id']) ? (int)$input['city_id'] : NULL;
$description = $conn->real_escape_string($input['description'] ?? '');
$sponsorship = $conn->real_escape_string($input['sponsorship'] ?? '');

// Validate required fields
if (!$name || !$email || !$contact || !$description || !$state_id || !$city_id || !$sponsorship) {
    echo json_encode(["success" => false, "message" => "Please fill all required fields"]);
    exit;
}

// Check if email or contact already exists
$checkSql = "SELECT id FROM bookings WHERE email='$email' OR contact='$contact' LIMIT 1";
$result = $conn->query($checkSql);

if ($result && $result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "This email or contact number is already registered"]);
    exit;
}

// Insert into database
$sql = "INSERT INTO bookings (name, email, contact, state_id, city_id, description, sponsorship)
        VALUES ('$name', '$email', '$contact', ".($state_id ?: 'NULL').", ".($city_id ?: 'NULL').", '$description', '$sponsorship')";

if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "Booking submitted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}

$conn->close();
?>
