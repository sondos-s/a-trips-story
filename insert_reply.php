<?php
session_start();

// Include your database connection file (UDB.php)
include 'UDB.php';

// Check if the user is logged in and is the owner
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] != 9) {
    header("Location: login.php"); // Redirect to login page if not logged in or not the owner
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the message ID and reply content from the form
    $messageID = $_POST["message_id"];
    $reply = $_POST["reply"];

    // Update the reply in the database
    $sql = "UPDATE messages SET reply = '$reply' WHERE id = $messageID";

    if ($conn->query($sql) === TRUE) {
        // Reply successfully stored
        header("Location: view_user_messages.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    // Handle cases where the form was not submitted
    header("Location: view_user_messages.php");
    exit();
}
?>
