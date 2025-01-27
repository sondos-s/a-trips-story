<?php

$password_reset_successful = false;


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $mysqli = require __DIR__ . "/UDB.php";
    $new_password = $_POST["password"];
    $token = $_POST["token"];

    // Verify token and expiration date
    $stmt = $mysqli->prepare("SELECT email, expires FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($email, $expires);
    $stmt->fetch();
    $stmt->close();

    $now = new DateTime();
    $expires = new DateTime($expires);

    if ($now < $expires) {
        
        if (strlen($password) < 8 || !preg_match("/\d/", $password) || !preg_match("/[a-zA-Z]/", $password)) {
            $errorMessage = "Password must be at least 8 characters long and contain both numbers and letters.";
        } elseif ($password !== $confirmPassword) {
            $errorMessage = "Passwords do not match.";
        }
        // Token is valid, update the password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE users SET passwordUser = ? WHERE emailAddress = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();
        $stmt->close();

        // Delete the token to prevent reuse
        $stmt = $mysqli->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();

        $password_reset_successful = true;
    }
}
