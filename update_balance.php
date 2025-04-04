<?php
header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'].'/../config.php');

$servername = DB_HOST; // Remove http://
$username = DB_USERNAME; 
$password = DB_PASSWORD; 
$dbname = DB_NAME; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['username']) && isset($data['balance'])) {
    $username = $data['username'];
    $new_balance = floatval($data['balance']);

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE username = ?");
    $stmt->bind_param("ds", $new_balance, $username); // "ds" means double (decimal) and string
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Balance updated"]);
    } else {
        echo json_encode(["error" => "Update failed"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request"]);
}

$conn->close();
?>
