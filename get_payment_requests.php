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

// Fetch all payment requests
$query = "SELECT id, username, card_number, amount, status FROM payment_request";
$result = $conn->query($query);

// Check if there are any results
if ($result->num_rows > 0) {
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    echo json_encode($requests);
} else {
    echo json_encode(["error" => "No payment requests found"]);
}

$conn->close();
?>
