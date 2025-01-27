<?php
/// the code for sesstion starts here
$is_invalid = false;

// Set the session timeout to 24 hours (in seconds)
$session_timeout = 24*60*60; // 24 hours x 60 minutes x 60 seconds

// Set session cookie parameters (optional)
session_set_cookie_params($session_timeout);

// Set the PHP session timeout
ini_set('session.gc_maxlifetime', $session_timeout);

session_start(); // Start the session
//and ends here 

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $mysqli = require __DIR__ . "/UDB.php";

    $sql = sprintf("SELECT * FROM users WHERE username='%s'",
        $mysqli->real_escape_string($_POST["username"]));
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if ($user) {
        if ($_POST["username"] == "owner" && $_POST["password"] == "password1") {
            session_regenerate_id();
            $_SESSION["user_id"] = $user["id"];
            header("Location: HomeScreen.php");
            exit;
        } elseif ($_POST["password"] === $user["passwordUser"]) {
            if ($user["verified"] == 0) {
                $error_message = "Your email is not verified. <br>Please verify your email first.";
            } else {
                session_regenerate_id();
                $_SESSION["user_id"] = $user["id"];
                header("Location: Home_FrontEnd.php");
                exit;
            }
        } else {
            $error_message = "Wrong password";
        }
    } else {
        $error_message = "You have not registered yet";
    }
} else {
    $error_message = false;
}

?>
