<?php
// Include the database connection file
include 'UDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $teamId = $_POST["teamId"];
    $memberName = htmlspecialchars($_POST["memberName"]);
    $memberDescription = htmlspecialchars($_POST["memberDescription"]);

    // Update the database
    $updateSql = "UPDATE aboutteam SET memberName = '$memberName', memberDescription = '$memberDescription' WHERE teamId = $teamId";

    if ($conn->query($updateSql) === TRUE) {
        // Update successful
        echo "Team member changes saved successfully.";
    } else {
        // Handle the error
        echo "Error updating team member information: " . $conn->error;
    }
}
?>
