<?php
session_start(); // Start the session for user authentication

// Check if the user ID is 9 to determine if it's an owner
$isOwner = false;
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 9) {
    $isOwner = true;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>FAQ</title>
</head>

    <body>
        <?php 
            if ($isOwner) {
                include 'Header_Owner.php'; // Include owner header if the user is an owner
            } else {
                include 'Header.php'; // Include regular user header
            }
        ?>
        <div class="compactSidebarManageFAQ">
        <?php if ($isOwner): ?>
            <button onmouseover="playSound()"><a href="FAQ_Manage_FrontEnd.php" class="manage-a"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Manage</button></a>
        <?php endif; ?>
        </div>
        <div class="faq">
        <h2 style="margin-top: 50px;">Frequently Asked Questions</h2>
        <br><br><br>
            <?php
            // Include the database connection code from UDB.php
            include "UDB.php";

            // Query the database to fetch FAQ records
            $query = "SELECT * FROM faq";
            $result = $mysqli->query($query);

            if (!$result) {
                die("Query failed: " . $mysqli->error);
            }

            while ($row = $result->fetch_assoc()) {
                echo '<div class="faq-question">';
                echo '<div class="question-header">';
                echo '<p>' . $row['question'] . '</p>';
                echo '<span class="plus-icon">+</span>';
                echo '</div>';
                echo '<div class="answer">';
                echo '<p>' . $row['answer'] . '</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <audio id="soundPop">
            <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
        </audio>

    </body>

</html>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const questionHeaders = document.querySelectorAll(".question-header");

    questionHeaders.forEach(header => {
        header.addEventListener("click", () => {
            const question = header.parentElement;
            const answer = question.querySelector(".answer");
            const plusIcon = header.querySelector(".plus-icon");
            const expanded = question.classList.toggle("expanded");

            if (expanded) {
                answer.style.maxHeight = answer.scrollHeight + "px";
                plusIcon.textContent = "-";
                plusIcon.style.fontSize = "24px";
            } else {
                answer.style.maxHeight = "0";
                plusIcon.textContent = "+";
                plusIcon.style.fontSize = "24px";
            }
        });
    });
});

</script>

<script>
    // Function to play the sound
    function playSound() {
        var sound = document.getElementById("soundPop");
        if (sound) {
            sound.currentTime = 0; // Reset the sound to the beginning
            sound.play();
        } else {
            console.log("Sound element not found.");
        }
    }
</script>
