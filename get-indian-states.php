

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
$country_id = 101;

$stmt = $conn->prepare("SELECT id, name FROM states WHERE country_id = ? ORDER BY name");
$stmt->bind_param("i", $country_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
