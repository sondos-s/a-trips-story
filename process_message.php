<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}

// Include your database connection file (UDB.php)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_SESSION["user_id"];
    $message = $_POST["message"];

    // Insert the message into the database
    $sql = "INSERT INTO messages (user_id, message) VALUES ('$userID', '$message')";
    if ($conn->query($sql) === TRUE) {
        // Message successfully stored
        header("Location: contact_us.php?message_sent=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!-- Include your footer here -->