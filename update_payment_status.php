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
if (isset($data['id']) && isset($data['status'])) {
    $id = intval($data['id']); // Payment request ID
    $status = $data['status']; // New status ('complete' or 'not_complete')

    // Validate status
    if (!in_array($status, ['complete', 'not_complete'])) {
        echo json_encode(["error" => "Invalid status value"]);
        exit();
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE payment_request SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id); // "si" means string (status) and integer (id)
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Payment request status updated"]);
    } else {
        echo json_encode(["error" => "Error updating status"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request, missing parameters."]);
}

$conn->close();
?>