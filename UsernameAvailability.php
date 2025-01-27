<?php
// Include the database connection code (UDB.php or your equivalent)
require 'UDB.php';

// Check if the username is sent via POST
if (isset($_POST['username'])) {
    $username = $_POST['username'];

    // Prepare and execute a database query to check if the username exists
    $query = "SELECT username FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check the number of rows returned (0 if username is available, 1 if it's taken)
    if ($stmt->num_rows === 1) {
        echo 'taken';
    } else {
        echo 'available';
    }

    // Close the database connection
    $stmt->close();
    $mysqli->close();
} else {
    echo 'Invalid request';
}
?>
