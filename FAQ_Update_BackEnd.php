<?php
// Include your database connection code (e.g., UDB.php)
include 'UDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form was submitted

    // Validate and sanitize the input data
    $id = $_POST["id"];
    $question = $_POST["question"];
    $answer = $_POST["answer"];

    // Add more validation if needed (e.g., checking for empty fields)

    if (empty($id)) {
        // If the ID is empty, it's a new entry, so insert it
        $query = "INSERT INTO faq (question, answer) VALUES (?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $question, $answer);
    } else {
        // If there's an ID, it's an update, so update the existing entry
        $query = "UPDATE faq SET question = ?, answer = ? WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssi", $question, $answer, $id);
    }

    if ($stmt->execute()) {
        // Successfully inserted or updated the data
        header("Location: FAQ_Manage_FrontEnd.php"); // Redirect to the page you want
        exit();
    } else {
        // Handle database error
        echo "Error: " . $stmt->error;
    }

    // Close the database connection
    $stmt->close();
    $mysqli->close();
}
?>
