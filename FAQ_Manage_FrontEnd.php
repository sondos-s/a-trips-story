<?php
session_start(); // Start the session for user authentication

// Check if the user ID is 9 to determine if it's an owner
$isOwner = false;
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 9) {
    $isOwner = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>FAQ Content</title>
</head>

<body>
    <?php include 'Header_Owner.php' ?>
    <div class="compactSidebar">
        <?php if ($isOwner): ?>
            <button id="button1"><a href="FAQ_FrontEnd.php" onmouseover="playSound()"><i class="fa fa-undo"></i>&nbsp;&nbsp;Back to FAQ View</a></button>
        <?php endif; ?>
    </div>
    <?php
    include 'UDB.php';

    // Fetch trip data from the trips table
    $query = "SELECT * FROM faq ORDER BY id ASC"; // Order by ID
    $result = $mysqli->query($query);
    ?>

    <div class="container-faq">
        <div class="add-button-faq" onmouseover="playSound()">
            <a class="add-button-faq-link" id="manageFAQ"><i class="fa fa-plus fa-lg"></i></a>
        </div>
        <div class="table-wrapper-faq">
            <div class="table-container-faq">
                <h3 style="font-size: 18px; padding-top: 50px;">Frequently Asked Questions</h3>
                <br><br>
                <table>
                    <tr>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr class="table-row">';
                        echo '<td class="question-cell">' . $row["question"] . '</td>';
                        echo '<td class="answer-cell">' . $row["answer"] . '</td>';
                        echo '<td><a href="#" class="edit-icon" onclick="openEditPopup(' . $row["id"] . ', \'' . $row["question"] . '\', \'' . $row["answer"] . '\')"><i class="fa fa-pencil-square-o fa-lg"></i></a></td>';
                        echo '<td><a href="FAQ_Delete_BackEnd.php?id=' . $row["id"] . '" onclick="confirmDelete(' . $row["id"] . '); return false;"><i class="fa fa-trash fa-lg"></i></a></td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
    <audio id="soundPop">
        <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
    </audio>
    <div class="popup-container" id="popup">
        <div class="popup-content" style="width: 400px;">
            <a class="popup-close" onclick="closePopup()">×</a>
            <br><br>
            <h5 class="popup-title" style="font-size: 18px;">Update FAQ Content</h5>
            <br>
            <hr>
            <br>
            <form method="post" action="FAQ_Update_BackEnd.php">
                <input type="hidden" id="id" name="id" value="">
                <div class="form-group-contactus">
                    <label for="question" style="float: left; padding-left: 45px;">Questions: </label>
                    <textarea style="height: 10px; width: 300px; font-size: 14px;" id="question" name="question" value="" required></textarea>
                </div>
                <div class="form-group-contactus">
                    <label for="answer" style="float: left; padding-left: 45px;">Answer:</label>
                    <textarea style="height: 10px; width: 300px; font-size: 14px;" id="answer" name="answer" value="" required></textarea>
                </div>
                <br><br><br>
                <button class="contactus-save-btn" id="contactus-save-btn" type="submit" name="submit">Save Changes</button>
            </form>
            <br><br>
        </div>
    </div>
</body>

</html>

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

<script>
        function confirmDelete(id) {
            var result = confirm("Are you sure you want to delete this FAQ entry?");
            if (result) {
                // User clicked "OK," proceed with the delete action
                window.location.href = 'FAQ_Delete_BackEnd.php?id=' + id;
            } else {
                // User clicked "Cancel," do nothing
            }
        }
</script>

<script>
    window.onload = function () {
        // openPopup(); // You can choose to open the popup on page load, as you prefer.
    };

    function openPopup() {
        document.getElementById("popup").classList.add("active");
    }

    function closePopup() {
        document.getElementById("popup").classList.remove("active");
    }

    // Add an event listener to "editContactInfo" button to open the popup when it's clicked
    document.getElementById("manageFAQ").addEventListener("click", function () {
        openPopup();
    });
</script>

<script>
    // Function to reset the popup form
    function resetPopup() {
        document.getElementById("question").value = "";
        document.getElementById("answer").value = "";
    }

    // Add an event listener to the "Add" button to open the popup without data
    document.getElementById("manageFAQ").addEventListener("click", function () {
        resetPopup();
        openPopupWithData("", ""); // Open the popup without data
    });
</script>

<script>
    function openEditPopup(id, question, answer) {
        document.getElementById("popup").classList.add("active");
        document.getElementById("id").value = id;
        document.getElementById("question").innerHTML = question;
        document.getElementById("answer").innerHTML = answer;
    }
</script>


