<?php
// Include your database connection code (e.g., UDB.php)
include 'UDB.php';

// Check if the FAQ ID is provided in the URL
if (isset($_GET['id'])) {
    $faqId = $_GET['id'];

    // Prepare and execute the SQL query to delete the FAQ entry
    $deleteQuery = "DELETE FROM faq WHERE id = ?";
    $stmt = $mysqli->prepare($deleteQuery);

    // Bind the FAQ ID as a parameter
    $stmt->bind_param("i", $faqId);

    if ($stmt->execute()) {
        // FAQ entry was successfully deleted
        header("Location: FAQ_Manage_FrontEnd.php"); // Redirect back to the FAQ page
        exit();
    } else {
        // An error occurred while deleting the FAQ entry
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$mysqli->close();
?>
