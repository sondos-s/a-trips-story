<?php
// Include your database connection file (UDB.php)
require_once 'UDB.php';

// Check if a link is submitted via a form
if (isset($_POST['submit'])) {
    // Get the link from the form
    $link = $_POST['link'];

    // Validate the link (you can add more validation as needed)
    if (filter_var($link, FILTER_VALIDATE_URL)) {
        // Insert the link into the database
        $sql = "INSERT INTO instasection (link) VALUES (?)";
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("s", $link);
            $stmt->execute();
            $stmt->close();
            
            // Display a success message
            echo "Link saved successfully!";
        } else {
            // Display an error message for the SQL statement
            echo "Error: " . $mysqli->error;
        }
    } else {
        // Display an error message for invalid URLs
        echo "Invalid URL. Please enter a valid link.";
    }
}

// Display the form to submit links
?>