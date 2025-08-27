
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$host = "localhost";
require_once __DIR__ . '/config/db.php';


$conn = db_get_connection();
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
