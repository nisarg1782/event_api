<?php
// Centralized database configuration and connection helper

// Use environment variables when available; fallback to sensible defaults for local dev
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'event';

/**
 * Get a mysqli connection or exit with a JSON error response on failure.
 * @return mysqli
 */
function db_get_connection(): mysqli {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;

    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

    if ($conn->connect_error) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    // Ensure proper charset
    $conn->set_charset('utf8mb4');

    return $conn;
}

?>
