<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 0; // Enable debugging (0 for no debugging)
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com'; // Your SMTP host
    $mail->SMTPAuth = true;
    $mail->Username = 'atripsstory@gmail.com'; // Your SMTP username
    $mail->Password = 'Balkes13579'; // Your SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption
    $mail->Port = 587; // TCP port to connect to

    $userEmail = $_POST["email"];


    $mysqli = require __DIR__ . "/UDB.php"; // Establish a database connection

// Assuming you have already obtained the user's email address (e.g., $_POST["email"])

// Query the database to retrieve the verification token associated with the user's email
    $selectSql = "SELECT verification_code FROM users WHERE emailAddress = '$userEmail'";
    $stmt = $mysqli->prepare($selectSql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User found, retrieve the verification token
        $row = $result->fetch_assoc();
        $verificationToken = $row["verification_code"];
    }

    $verificationLink = "https://example.com/verify.php?token=" . $verificationToken; // Generate the verification link
    $recipientName = $_POST["first_name"]; // Get the recipient's name (optional)

    // Recipients
    $mail->setFrom('your_email@example.com', 'Your Name');
    $mail->addAddress($userEmail, $recipientName); // Add recipient email and name

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Email Verification'; // Email subject
    $mail->Body = 'Click the following link to verify your email address: <a href="' . $verificationLink . '">Verify Email</a>'; // Email content

    // Send the email
    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo 'Email could not be sent. Error: ', $mail->ErrorInfo;
}


?>