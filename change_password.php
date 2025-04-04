<?php
header("Content-Type: application/json");

include($_SERVER['DOCUMENT_ROOT'].'/../config.php');


// Database connection credentials
$servername = DB_HOST; 
$username = DB_USERNAME; 
$password = DB_PASSWORD; 
$dbname = DB_NAME; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["username"]) || !isset($data["new_password"])) {
    die(json_encode(["success" => false, "message" => "Invalid input"]));
}

$username = $conn->real_escape_string($data["username"]);
$new_password = $conn->real_escape_string($data["new_password"]);

// Update password (STORING AS PLAIN TEXT — INSECURE IN REALITY!)
$sql = "UPDATE users SET password_hash = '$new_password' WHERE username = '$username'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Password updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating password"]);
}

$conn->close();
?>
