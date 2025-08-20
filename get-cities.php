
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
$host = "localhost";
$user = "root"; // change if needed
$pass = "";     // change if needed
$db   = "event";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}
$state_id = isset($_GET['state_id']) ? (int)$_GET['state_id'] : 0;

$stmt = $conn->prepare("SELECT id, name FROM cities WHERE state_id = ? ORDER BY name");
$stmt->bind_param("i", $state_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
