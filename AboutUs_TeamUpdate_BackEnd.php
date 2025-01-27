<?php
session_start();
include 'UDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editTeamMembers"])) {
    // Loop through the submitted data for each team member
    foreach ($_POST["teamMembers"] as $teamMemberData) {
        // Sanitize and validate the inputs for each team member
        $memberName = htmlspecialchars(trim($teamMemberData["memberName"]), ENT_QUOTES, 'UTF-8');
        $memberDescription = htmlspecialchars(trim($teamMemberData["memberDescription"]), ENT_QUOTES, 'UTF-8');
        $teamId = intval($teamMemberData["teamId"]);

        // Construct the SQL query to update team member information
        $updateSql = "UPDATE aboutteam SET memberName = '$memberName', memberDescription = '$memberDescription' WHERE teamId = $teamId";

        // Execute the SQL query
        if ($conn->query($updateSql) !== TRUE) {
            // Handle the error and display the specific error message
            echo "Error updating team member information: " . $conn->error;
            exit();
        }
    }

    // Redirect back to the About Us page or perform other actions upon successful update.
    header("Location: AboutUs_FrontEnd.php");
    exit();
}
?>
