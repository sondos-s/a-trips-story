<?php
session_start();
include 'UDB.php';

if (isset($_GET['token'])) {

    $verificationToken = $_GET['token'];

    $mysqli = require __DIR__ . "/UDB.php"; // Establish a database connection

    // Query the database to find the user with the given verification token
    $sql = "SELECT verification_code FROM users WHERE verification_code = ? AND verified = 0 LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $verificationToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, mark them as verified
        $updateSql = "UPDATE users SET verified = 1 WHERE verification_code = ?";
        $updateStmt = $mysqli->prepare($updateSql);
        $updateStmt->bind_param("s", $verificationToken);
        $updateStmt->execute();

        $_SESSION["error_message"] = "Email verified successfully!";
        header("Location: SignIn_FrontEnd.php");
    } else {
        $_SESSION["error_message"] = "Email Already verified. please Login !";
        header("Location: SignIn_FrontEnd.php");
    }
} else {
    echo "Invalid request.";
}
?>
