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

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['username'], $data['balance_before'], $data['balance_after'], 
          $data['prediction_amount'], $data['last_candle'], $data['result'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Ensure last_candle and result have valid ENUM values
$valid_candles = ['red', 'green', 'gray'];
$valid_results = ['win', 'lose', 'nothing'];

if (!in_array($data['last_candle'], $valid_candles) || !in_array($data['result'], $valid_results)) {
    echo json_encode(["success" => false, "message" => "Invalid candle color or result"]);
    exit;
}

// Prepare and execute the SQL statement
$stmt = $conn->prepare("INSERT INTO prediction (username, balance_before, balance_after, prediction_amount, last_candle, result) 
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sddsss", 
    $data['username'], 
    $data['balance_before'], 
    $data['balance_after'], 
    $data['prediction_amount'], 
    $data['last_candle'], 
    $data['result']
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Prediction inserted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Insertion failed"]);
}

// Close connections
$stmt->close();
$conn->close();

?>
