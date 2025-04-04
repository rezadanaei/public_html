<?php
header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'].'/../config.php');


$servername = DB_HOST;
$username = DB_USERNAME; // Change if needed
$password = DB_PASSWORD; // Change if needed
$dbname = DB_NAME; // Change to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input data from the frontend (login credentials)
    $inputData = json_decode(file_get_contents('php://input'), true);

    $username = $conn->real_escape_string($inputData['username']);
    $password = $inputData['password']; // Plain password (to compare with the hash)

    // Fetch user data based on the provided username
    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if ($password == $user['password_hash']) {
            // Password is correct, generate token (or session ID)
            $token = generateToken($user['username']); // You can use JWT or a simple session token

            // Response with success, user data, and token
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "token" => $token,
                "user" => [
                    "username" => $user['username'],
                    "card_number" => $user['card_number'],
                    "phone_number" => $user['phone_number'],
                    "balance" => $user['balance'],
                    "status" => $user['status']
                ]
            ]);
        } else {
            // Password does not match
            echo json_encode(["status" => "error", "message" => "Invalid username or password"]);
        }
    } else {
        // Username not found
        echo json_encode(["status" => "error", "message" => "Invalid username or password"]);
    }
}

$conn->close();

// Function to generate a token (for example, JWT or simple token)
function generateToken($username) {
    // Here, for simplicity, we're using a simple token based on the username and time
    return md5($username . time()); // A better approach would be using JWT or a session token
}
?>
