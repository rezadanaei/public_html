<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'].'/../config.php');


// Database connection credentials
$servername = DB_HOST; 
$username_db = DB_USERNAME; 
$password = DB_PASSWORD; 
$dbname = DB_NAME; 

// Create connection
$conn = new mysqli($servername, $username_db, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required fields are provided
if (isset($data['username']) && isset($data['cardNumber']) && isset($data['amount'])) {
    $username = $data['username'];
    $cardNumber = $data['cardNumber'];
    $amount = floatval($data['amount']);
    $status = 'pending'; // Default status set to 'pending'

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO payment_request (username, card_number, amount, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $username, $cardNumber, $amount, $status); // 's' for string, 'i' for integer, 'd' for decimal
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Payment request created successfully"]);
    } else {
        echo json_encode(["error" => "Error inserting payment request: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request, missing parameters."]);
}

$conn->close();
?>
