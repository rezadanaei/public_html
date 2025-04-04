<?php

header("Content-Type: application/json");

include($_SERVER['DOCUMENT_ROOT'].'/../config.php');


// Database connection settings
$host = DB_HOST;
$dbname = DB_NAME;
$username = DB_USERNAME;
$password = DB_PASSWORD;

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Get the username from the URL
$username = isset($_GET['username']) ? $_GET['username'] : '';

if (empty($username)) {
    echo json_encode(["success" => false, "message" => "Username is required"]);
    exit;
}

// Fetch predictions for the user
$stmt = $conn->prepare("SELECT id, balance_before, balance_after, prediction_amount, last_candle, result, created_at FROM prediction WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$predictions = [];
while ($row = $result->fetch_assoc()) {
    $predictions[] = $row;
}

$stmt->close();
$conn->close();

// Return JSON response
echo json_encode(["success" => true, "predictions" => $predictions]);

?>
