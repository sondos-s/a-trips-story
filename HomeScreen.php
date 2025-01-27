<?php
session_start();

// Check if the user ID is 9 to determine if it's an owner
$isOwner = false;
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 9) {
    $isOwner = true;
}

if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__."/UDB.php";
    $sql = "SELECT * FROM `users` WHERE id={$_SESSION["user_id"]}";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();
}
?>
<?php include 'Feature_Instagram_Add_BackEnd.php' ?>
<?php include 'BitInfo_Update_BackEnd.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/HomeViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Home</title>
</head>

<body>
    <?php
        if ($isOwner) {
            include 'Header_Owner.php'; // Include owner header if the user is an owner
        } else {
            include 'Header.php'; // Include regular user header
        }
        ?>

        <div class="welcome-container">
            <?php if ($isOwner): ?>
                <h1>Welcome Balkes!</h1>
            <?php elseif (isset($user)): ?>
                <h1>Welcome <?= htmlspecialchars($user["firstName"]) ?> 👋</h1>
            <?php else: ?>
                <h1>Welcome 👋</h1>
            <?php endif; ?>
            <br><br><br>
        </div>
    <?php if ($isOwner): ?>
        <div class="compactSidebar">
            <button id="button1" onmouseover="playSound()"><a href="Home_FrontEnd.php"><i class="fa fa-user"></i>&nbsp;&nbsp;Customer Mode</a></button>
            <div class="popup-container" id="popup">
                <div class="popup-content" style="width: 400px;">
                    <a class="popup-close" onclick="closePopup()">×</a>
                    <div class="instagram-icon-container">
                        <i class="fa fa-instagram fa-3x popup-icon"></i>
                    </div>
                    <br><br>
                    <h5 class="popup-title" style="font-size: 18px;">Save Links of Instagram Posts</h5>
                    <br>
                    <hr>
                    <br>
                    <form method="post">
                        <div class="form-group-link">
                            <label for="link">Link:</label>
                            <input style="height: 5px; width: 200px;" type="text" id="link" name="link" required>
                        </div>
                        <button class="link-save-btn" type="submit" name="submit">Save Link</button>
                    </form>
                    <br><br>
                </div>
            </div>
            <button id="button2" onmouseover="playSound()">
                <i class="fa fa-instagram"></i>&nbsp;&nbsp;Instagram Cards
            </button>
            <button id="button4" onmouseover="playSound()">
                <i class="fa fa-question-circle"><a href="FAQ_FrontEnd.php"></i>Handle FAQ</a>
            </button>
            <button id="button3" onmouseover="playSound()">
                <i class="fa fa-envelope"><a href="ContactUs_FrontEnd.php"></i>&nbsp;&nbsp;Contact Us</a>
            </button>
            <button id="button5" onmouseover="playSound()">
                <i class="fa fa-info"><a href="AboutUs_FrontEnd.php"></i>&nbsp;&nbsp;About Us</a>
            </button>
            <button id="button6" onmouseover="playSound()">
                <i class="fa fa-pencil-square"></i>&nbsp;&nbsp;Bit Payment</a>
            </button>
            <div class="popup-container" id="popup-bit">
                <div class="popup-content" style="width: 400px;">
                    <a class="popup-close" onclick="closePopupBit()">×</a>
                    <br><br>
                    <h5 class="popup-title" style="font-size: 18px;">Bit - Information</h5>
                    <br>
                    <hr>
                    <br>
                    <form method="post">
                        <div class="form-group-bit">
                            <label for="bitInfo">Information:</label>
                            <textarea style="height: 100px; width: 100%;" id="bitInfo" name="bitInfo" required><?php echo htmlspecialchars($currentBitInfo); ?></textarea>
                        </div>
                        <button class="bit-save-btn" type="submit" name="submit-bit">Save</button>
                    </form>
                    <br><br>
                </div>
            </div>
            <button id="button7" onmouseover="playSound()">
                <i class="fa fa-comments"><a href="Messages.php"></i>&nbsp;&nbsp;Messages Forum</a>
            </button>
        </div>
    </div>
        <div class="ownerActionsContainerTitle">
        <br><br><br>
        <h2>What would you like to do today?</h2>
        <br><br><br>
    </div>
    <div class="ownerActionsContainer">
        <br><br><br>
        <a href="TripsManagement_FrontEnd.php">
            <img src="ViewStyles/OwnerImages/tripsmanagement.png" alt="Trips Management">
            <span>Trips Management</span>
        </a>
        <a href="Report_Dashboard_FrontEnd.php">
            <img src="ViewStyles/OwnerImages/reports.png" alt="Reports">
            <span>Reports</span>
        </a>
        <a href="TripsBookings_FrontEnd.php">
            <img src="ViewStyles/OwnerImages/bookings.png" alt="Bookings">
            <span>Bookings</span>
        </a>
        <a href="TripsBookingsManagement_FrontEnd.php">
            <img src="ViewStyles/OwnerImages/viewalltrips.png" alt="View All Trips">
            <span>Bookings Requests</span>
        </a>
    </div>
    <audio id="soundPop">
        <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
    </audio>
    <?php else: ?>
        <div class="actionsContainerTitle">
        <br><br><br>
        <h3>Nice to see you! Check out our trips</h3>
        <br><br><br>
    </div>
    <div class="tripCategoriesContainer">
    <h2>Categories:</h2>
        <br><br><br>
        <a href="#">
            <img src="ViewStyles/TripsCategories/hikingtrips.png">
            <span>Hiking</span>
        </a>
        <a href="#">
            <img src="ViewStyles/TripsCategories/heritagetrips.png">
            <span>Heritage</span>
        </a>
        <a href="#">
            <img src="ViewStyles/TripsCategories/campingtrips.png">
            <span>Camping</span>
        </a>
        <a href="#">
            <img src="ViewStyles/TripsCategories/culturaltrips.png">
            <span>Cultural</span>
        </a>
        <a href="#">
            <img src="ViewStyles/TripsCategories/cruisetrips.png">
            <span>Cruise</span>
        </a>
        <a href="#">
            <img src="ViewStyles/TripsCategories/safaritrips.png">
            <span>Safari</span>
        </a>
    </div>
    <?php endif; ?>

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
    document.getElementById("button1").addEventListener("click", function() {
        toggleSidebar();
    });

    document.getElementById("button2").addEventListener("click", function() {
        toggleSidebar();
    });

    document.getElementById("button3").addEventListener("click", function() {
        toggleSidebar();
    });
    
    document.getElementById("button4").addEventListener("click", function() {
        toggleSidebar();
    });

    document.getElementById("button5").addEventListener("click", function() {
        toggleSidebar();
    });

    document.getElementById("button6").addEventListener("click", function() {
        toggleSidebar();
    });

    document.getElementById("button7").addEventListener("click", function() {
        toggleSidebar();
    });

    // Function to toggle the sidebar
    function toggleSidebar() {
        const compactSidebar = document.querySelector(".compactSidebar");
        compactSidebar.classList.toggle("showSidebar");
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

    // Add an event listener to "button2" to open the popup when it's clicked
    document.getElementById("button2").addEventListener("click", function () {
        openPopup();
    });
</script>


<script>
    window.onload = function () {
        // openPopup(); // You can choose to open the popup on page load, as you prefer.
    };

    function openPopupBit() {
        document.getElementById("popup-bit").classList.add("active");
    }

    function closePopupBit() {
        document.getElementById("popup-bit").classList.remove("active");
    }

    // Add an event listener to "button2" to open the popup when it's clicked
    document.getElementById("button6").addEventListener("click", function () {
        openPopupBit();
    });
</script>



