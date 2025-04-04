
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['username'])) {
    // Check if username exists (GET request)
    $user = $conn->real_escape_string($_GET['username']);
    
    $sql = "SELECT username FROM users WHERE username = '$user' LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo json_encode(["status" => false, "message" => "This username is already taken."]);
    } else {
        echo json_encode(["status" => true, "message" => "Username is available."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'], $_POST['card_number'], $_POST['phone_number'], $_POST['action']) && $_POST['action'] == 'signup') {
    // Signup logic (POST request)
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']); // Hash the password
    $card_number = $conn->real_escape_string($_POST['card_number']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $email = $conn->real_escape_string($_POST['email']);

    // Check if username already exists
    $sql = "SELECT username FROM users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode(["status" => false, "message" => "This username is already taken."]);
    } else {
        // Insert new user into the database
        $sql = "INSERT INTO users (username, password_hash, card_number, phone_number, email) 
                VALUES ('$username', '$password', '$card_number', '$phone_number', '$email')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => true, "message" => "User registered successfully."]);
        } else {
            echo json_encode(["status" => false, "message" => "Error: " . $conn->error]);
        }
    }
}
else {
    echo json_encode(["error" => "Required data not provided."]);
    echo json_encode($_SERVER);
}

$conn->close();
?>
