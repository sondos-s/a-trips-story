<?php
session_start(); // Start the session for user authentication

// Check if the user ID is 9 to determine if it's an owner
$isOwner = false;
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 9) {
    $isOwner = true;
}
$isLoggedIn = isset($_SESSION['user_id']); 
?>
<!DOCTYPE html>
<html lang="en">
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
    <title>Contact Us</title>
    <style>
  .btn-messages{
    position: fixed;
    left: 50%;
    bottom: 10%;
    transform: translateX(-50%);
    background-color:#c7a6c2;
    size: 40px;
    
    display: inline-block;
                    outline: none;
                    cursor: pointer;
                    font-size: 16px;
                    line-height: 20px;
                    font-weight: 600;
                    border-radius: 8px;
                    padding: 14px 24px;
                    border: none;
                    transition: box-shadow 0.2s ease 0s, -ms-transform 0.1s ease 0s, -webkit-transform 0.1s ease 0s, transform 0.1s ease 0s;
                    background: linear-gradient(to right, rgb(230, 30, 77) 0%, rgb(227, 28, 95) 50%, rgb(215, 4, 102) 100%);
                    
                    display: inline-block;
                    outline: none;
                    cursor: pointer;
                    font-size: 16px;
                    line-height: 20px;
                    font-weight: 600;
                    border-radius: 8px;
                    padding: 14px 24px;
                    border: none;
                    transition: box-shadow 0.2s ease 0s, -ms-transform 0.1s ease 0s, -webkit-transform 0.1s ease 0s, transform 0.1s ease 0s;
                    background: linear-gradient(to right, rgb(230, 30, 77) 0%, rgb(227, 28, 95) 50%, rgb(215, 4, 102) 100%);
                    color: #fff;
                
                
  }
  
</style>
</head>
<body id="contactusscreen">
<?php 
    if ($isOwner) {
        include 'Header_Owner.php'; // Include owner header if the user is an owner
    } else {
        include 'Header.php'; // Include regular user header
    }
?>

<div class="compactSidebarEditContact">
    <?php if ($isOwner): ?>
        <button id="editContactInfo" onmouseover="playSound()"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Edit</button>
    <?php endif; ?>
</div>

<?php
include 'UDB.php'; // Include the database connection file

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve data from the form
        $phoneNumber = $_POST['phonenumber'];
        $instagramLink = $_POST['instagramacc'];
        $facebookLink = $_POST['facebookpage'];

        // Query to update the existing contact information
        $sql = "UPDATE contactinfo SET phonenumber='$phoneNumber', instagramacc='$instagramLink', facebookpage='$facebookLink' WHERE contactid = 1";

        if ($conn->query($sql) === TRUE) {
            echo "Contact information updated successfully.";
        } else {
            echo "Error updating contact information: " . $conn->error;
        }
    }

    // Query to fetch contact information
    $sql = "SELECT * FROM contactinfo";
    $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                $phoneNumber = $row["phonenumber"];
                $instagramLink = $row["instagramacc"];
                $facebookLink = $row["facebookpage"];

                // Output the data in your HTML structure
                echo '<div id="contain" style="height: 350px;">';
                echo '<h1><span>Contact us</span></h1>';
                echo '<br><br>';
                echo '<div class="row">';
                echo '<div class="column" id="leftbox">';
                echo '<i class="fa fa-phone fa-3x circle-icon-phone"></i>';
                echo '<h3>Phone Number</h3>';
                echo '<p style="font-family: \'Gill Sans\', \'Gill Sans MT\', Calibri, \'Trebuchet MS\', sans-serif; font-size: 18px;">' . $phoneNumber . '</p>';
                echo '</div>';
                echo '<div class="column" id="middlebox">';
                echo '<i class="fa fa-instagram fa-3x circle-icon-instagram"></i>';
                echo '<h3>Instagram</h3>';
                echo '<a href="' . $instagramLink . '"><p>' . 'Qest Meshwar | Instagram' . '</p></a>';
                echo '</div>';
                echo '<div class="column" id="rightbox">';
                echo '<i class="fa fa-facebook-f fa-3x circle-icon-facebook"></i>';
                echo '<h3>Facebook</h3>';
                echo '<a href="' . $facebookLink . '"><p>' . 'Qest Meshwar | Facebook' . '</p></a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "No contact information found in the database.";
        } 

    // Close the database connection
    $conn->close();
?>
    <br>
   <!-- ?php include 'chatbot.html'; ?>-->

    <div class="popup-container" id="popup">
        <div class="popup-content" style="width: 400px;">
            <a class="popup-close" onclick="closePopup()">×</a>
            <br><br>
            <h5 class="popup-title" style="font-size: 18px;">Edit Contact Information</h5>
            <br>
            <hr>
            <br>
            <form method="post">
                <div class="form-group-contactus">
                    <label for="phonenumber" style="float: left; padding-left: 45px;">Phone Number:</label>
                    <input style="height: 10px; width: 300px; font-size: 14px;" type="text" id="phonenumber" name="phonenumber" value="<?php echo $phoneNumber; ?>" required>
                </div>
                <div class="form-group-contactus">
                    <label for="instagramacc" style="float: left; padding-left: 45px;">Instagram:</label>
                    <input style="height: 10px; width: 300px; font-size: 14px;" type="text" id="instagramacc" name="instagramacc" value="<?php echo $instagramLink ?>" required>
                </div>
                <div class="form-group-contactus">
                    <label for="facebookpage" style="float: left; padding-left: 45px;">Facebook:</label>
                    <input style="height: 10px; width: 300px; font-size: 14px;" type="text" id="facebookpage" name="facebookpage" value="<?php echo $facebookLink ?>" required>
                </div>
                <br><br><br>
                <button class="contactus-save-btn" id="contactus-save-btn" type="submit" name="submit">Save Changes</button>
            </form>
            <br><br>
        </div>
    </div>
    <!-- button -->
    <?php if ($isLoggedIn): ?>
<a href="<?php echo $isOwner ? 'messages.php' : 'messages_customer.php'; ?>">
    <button class="btn-messages">Messages</button>
</a>
<?php endif; ?>
<audio id="soundPop">
        <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
    </audio>
</body>
</html>


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
document.getElementById("editContactInfo").addEventListener("click", function () {
    openPopup();
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