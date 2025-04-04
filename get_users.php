<?php
header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'].'/../config.php');

// Database credentials
$servername = DB_HOST;
$username = DB_USERNAME;
$password = DB_PASSWORD;
$dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Query to fetch users
$sql = "SELECT username, phone_number, card_number, balance, password_hash, status, email FROM users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'username' => $row['username'],
            'phone' => $row['phone_number'],
            'card' => $row['card_number'],
            'balance' => floatval($row['balance']), // Ensure balance is a float
            'status' => $row['status'],
            'password' => $row['password_hash'],
            'email' => $row['email']
        ];
    }
}

// Close the connection
$conn->close();

// Output JSON
echo json_encode($users);
?>
