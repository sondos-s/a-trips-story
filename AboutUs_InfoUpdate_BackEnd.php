<?php
    // Include the database connection file
    include 'UDB.php';

    // Initialize the variables to store the About Us content
    $aboutUsText = "";

    // Query to fetch the About Us content
    $sql = "SELECT aboutContent FROM aboutinfo WHERE aboutId = 1"; // Assuming you have one row for About Us content

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $aboutUsText = $row["aboutContent"];
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        // Sanitize and update the About Us text in the database
        $newAboutUsText = nl2br(htmlspecialchars($_POST["aboutUsText"], ENT_QUOTES, 'UTF-8'));
        
        $updateSql = "UPDATE aboutinfo SET aboutContent = '$newAboutUsText' WHERE aboutId = 1";
        
        if ($conn->query($updateSql) === TRUE) {
            // Update successful
            $aboutUsText = $newAboutUsText;
        } else {
            // Handle the error
            echo "Error updating About Us text: " . $conn->error;
        }
    }

    // Handle form submission for editing team member information
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editTeamMembers"])) {
        // Loop through the submitted data for each team member
        foreach ($_POST["teamMembers"] as $key => $teamMemberData) {
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
            }
            
        }

        // Optionally, you can redirect the user to the About Us page or perform other actions upon successful update.
    }
?>