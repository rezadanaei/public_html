<?php
// user_status.php

// Set content type to JSON
header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'].'/../config.php');

// Read the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required data is provided
if (isset($data['username']) && isset($data['action'])) {
    $username = $data['username'];
    $action = $data['action'];

    // Validate the action
    if ($action !== 'ban' && $action !== 'active') {
        echo json_encode(['message' => 'Invalid action.']);
        exit;
    }

    // Database connection (with provided credentials)
    $servername = DB_HOST;
    $username_db = DB_USERNAME; // Change if needed
    $password = DB_PASSWORD; // Change if needed
    $dbname = DB_NAME; // Change to your actual database name

    // Create connection
    $conn = new mysqli($servername, $username_db, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        echo json_encode(['message' => 'Connection failed: ' . $conn->connect_error]);
        exit;
    }

    // Update the user's status in the database
    $sql = "UPDATE users SET status = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $action, $username);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'User status updated successfully.']);
    } else {
        echo json_encode(['message' => 'Error updating status: ' . $stmt->error]);
    }

    // Close connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['message' => 'Invalid request.']);
}
?>
