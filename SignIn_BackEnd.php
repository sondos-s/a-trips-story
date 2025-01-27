<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$mysqli = require __DIR__ . "/UDB.php"; // Or any other way you connect to your database

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $identifier = $_POST["identifier"]; // This can be either a username or an email
    $password = $_POST["password"];

    // Prepare SQL statement to retrieve the user by username or email
    $sql = "SELECT * FROM users WHERE username = ? OR emailAddress = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        if ($user['username'] == "owner" || $user['emailAddress'] == "atripsstory@gamil.com") {

            if (password_verify($password, $user["passwordUser"])) {
                // Set session variables
                session_regenerate_id();
                $_SESSION['user_id'] = "9";

                // Set cookie with a 24-hour expiry time
                setcookie("user", session_id(), time() + 24 * 60 * 60, "/");
                header("Location: HomeScreen.php");
                exit;
            } else {
                $_SESSION["error_message"] = "Invalid password";
                header("Location: SignIN_frontEnd.php");
                exit;
            }
        } elseif ($user['username'] != "owner" && $user['emailAddress'] != "atripsstory@gamil.com") {

            if (password_verify($password, $user["passwordUser"])) {

                if ($user['verified'] == 1) {
                    // Set session variables
                    session_regenerate_id();
                    $_SESSION["user_id"] = $user["id"];

                    // Set cookie with a 24-hour expiry time
                    setcookie("user", session_id(), time() + 24 * 60 * 60, "/");

                    // Redirect to the user dashboard
                    header("Location: Home_FrontEnd.php");
                    exit;
                } else {
                    $_SESSION["error_message"] = "Your email is not verified. Please verify your email first.";
                    header("Location: SignIn_frontEnd.php");
                    exit;
                }
            } else {
                $_SESSION["error_message"] = "Invalid password";
                header("Location: SignIN_frontEnd.php");
                exit;
            }
        } else {
            $_SESSION["error_message"] = "Invalid Username or email or password";
            header("Location: SignIN_frontEnd.php");
            exit;
        }
    } else {
        $_SESSION["error_message"] = "Invalid Username or email";
        header("Location: SignIN_frontEnd.php");
        exit;
    }
}
