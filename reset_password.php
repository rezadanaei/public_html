<?php
header("Content-Type: application/json");

include($_SERVER['DOCUMENT_ROOT'].'/../config.php');

// Database connection
$host = DB_HOST;
$user = DB_USERNAME;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["message" => "Database connection failed"]));
}

// Get input data
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["email"])) {
    echo json_encode(["message" => "Email is required"]);
    exit;
}

$email = $conn->real_escape_string($data["email"]);

// Check if the email exists in the database
$sql = "SELECT username FROM users WHERE email = '$email'";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    echo json_encode(["message" => "Email not found"]);
    exit;
}

// Generate a random temporary password
function generateRandomPassword($length = 8) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

$newPassword = generateRandomPassword();

// Store the new password in the database (NOT HASHED for educational purposes)
$updateSql = "UPDATE users SET password_hash = '$newPassword' WHERE email = '$email'";
if (!$conn->query($updateSql)) {
    echo json_encode(["message" => "Failed to update password"]);
    exit;
}

// Send email using SMTP
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'mail.poolruf.com';  // Your SMTP host
    $mail->SMTPAuth = true;
    $mail->Username = 'support@poolruf.com'; // Your email
    $mail->Password = 'twPsH9jVRgS47G8';
    $mail->SMTPSecure = 'ssl';  // Using SSL since port 465
    $mail->Port = 465;

    // Email Details
    $mail->setFrom('support@poolruf.com', 'Support Team');
    $mail->addAddress($email);
    $mail->Subject = "Your Temporary Password";
    $mail->Body = "Hello,\n\nYour new temporary password is: $newPassword\n\nPlease log in and change it as soon as possible.\n\nThank you!";

    $mail->send();
    echo json_encode(["message" => "Temporary password sent to your email"]);
} catch (Exception $e) {
    echo json_encode(["message" => "Email could not be sent. Error: " . $mail->ErrorInfo]);
}

$conn->close();
?>
