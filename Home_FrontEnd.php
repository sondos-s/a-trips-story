<?php include 'Home_BackEnd.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/HomeViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
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
    <?php include 'Header.php'; ?>
    
        <div class="compactSidebar">
        <?php if ($isOwner): ?>
            <button id="button1"><a href="HomeScreen.php" onmouseover="playSound()"><i class="fa fa-undo"></i>&nbsp;&nbsp;Back to Owner Mode</a></button>
        <?php endif; ?>
        </div>

    <div class="page-container">
        <div class="welcome-container">

        </div>
        <div class="actionsContainerTitle">
            <div>
            <?php if (isset($user)): ?>
                <h1 style="font-family: 'Bitter'; align-items: center;">Welcome  <?= htmlspecialchars($user["firstName"]) ?> 👋<br><br><br><br><br><br></h1>
            <?php else: ?>
                <h1 style="font-family: 'Bitter'; align-items: center;">Welcome 👋<br><br><br><br><br><br></h1>
            <?php endif; ?>    
            </div>
            <div>
                <h2 style="align-items: center;">Nice to see you! Check out our trips</h2>
            </div>
            <br><br><br>
        </div>

        <!-- Categories -->
        <div class="tripCategoriesContainer">
            <h2>Categories:</h2>
            <br><br><br>
            <a href="TripsByCategory_FrontEnd.php?category=Hiking">
                <img src="ViewStyles/TripsCategories/hikingtrips.png">
                <span>Hiking</span>
            </a>
            <a href="TripsByCategory_FrontEnd.php?category=Heritage">
                <img src="ViewStyles/TripsCategories/heritagetrips.png">
                <span>Heritage</span>
            </a>
            <a href="TripsByCategory_FrontEnd.php?category=Camping">
                <img src="ViewStyles/TripsCategories/campingtrips.png">
                <span>Camping</span>
            </a>
            <a href="TripsByCategory_FrontEnd.php?category=Cultural">
                <img src="ViewStyles/TripsCategories/culturaltrips.png">
                <span>Cultural</span>
            </a>
            <a href="TripsByCategory_FrontEnd.php?category=Cruise">
                <img src="ViewStyles/TripsCategories/cruisetrips.png">
                <span>Cruise</span>
            </a>
            <a href="TripsByCategory_FrontEnd.php?category=Safari">
                <img src="ViewStyles/TripsCategories/safaritrips.png">
                <span>Safari</span>
            </a>
        </div>

        <div class="trips-container">
            <div class="trips-list">
                <h3 style="padding-top: 2000px; display: flex; text-decoration: none;"><a href="TripsByDate_FrontEnd.php?date_type=upcoming" style="text-decoration: none; color: black;">Upcoming Planned Trips</h3></a>
                <div class="content-container">
                    <?php foreach ($plannedTrips as $trip) : ?>
                        <div class="content-item" style="background-color: white; margin-right: 20px; padding: 20px 20px; border-radius: 10px; align-items: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                            <a style="text-decoration: none;" href="TripDetails_FrontEnd2.php?trip_id=<?php echo $trip['trip_id']; ?>&tripTitle=<?php echo urlencode($trip['title']); ?>">
                                <img src="<?php echo $trip['image']; ?>" alt="Trip Image" style="width: 150px; height: 150px;">
                                <p><strong><?php echo $trip['title']; ?></strong></p>
                                <p style="font-size: 14px;">Date: <?php echo $trip['date']; ?></span></p>
                                <p style="font-size: 14px;">Price: <?php echo $trip['price']; ?></span></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <br>
            <div class="trips-list">
                <h3 style="padding-top: 50px; display: flex; text-decoration: none;"><a href="TripsByDate_FrontEnd.php?date_type=past" style="text-decoration: none; color: black;">Past Trips</h3></a>
                <!-- arrow buttons for navigation -->
                <button class="prev-button" onclick="changePastTrips(-1)">&#10094;</button>
                <button class="next-button" onclick="changePastTrips(1)">&#10095;</button>
                <div class="past-trip-row active">
                    <?php foreach ($pastTrips as $index => $trip) : ?>
                        <div class="content-item-past" style="background-color: white; margin-right: 20px; padding: 20px 20px; border-radius: 10px; align-items: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                            <a style="text-decoration: none;" href="TripDetails_FrontEnd2.php?trip_id=<?php echo $trip['trip_id']; ?>&tripTitle=<?php echo urlencode($trip['title']); ?>">
                                <img src="<?php echo $trip['image']; ?>" alt="Trip Image" style="width: 150px; height: 150px;">
                                <p><strong><?php echo $trip['title']; ?></strong></p>
                                <p style="font-size: 14px; text-decoration: none;">Date: <?php echo $trip['date']; ?></p>
                                <p style="font-size: 14px; text-decoration: none;">Price: <?php echo $trip['price']; ?></p>
                            </a>
                        </div>
                    <?php
      if (($index + 1) % 5 === 0) {
        echo '</div><div class="past-trip-row">';
      }
      ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <audio id="soundPop">
                <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
            </audio>
            
        </div> <!-- End of the page-container -->
        <?php include 'Feature_Calendar_FrontEnd.php' ?>
        <h3 class="instagram-header">✨ Discover the Joy and Adventure: Join Us for These Amazing and Unforgettable Moments! 📸🌄</h3>
        <?php include 'Feature_Instagram_BackEnd.php' ?>

        <?php include 'Footer.php' ?>

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

    // Attach hover event listeners to category links
    var categoryLinks = document.querySelectorAll('.tripCategoriesContainer a');
    categoryLinks.forEach(function (link) {
        link.addEventListener('mouseenter', playSound);
    });
</script>

<script>
    var currentPastTripRow = 0;
    var pastTripRows = document.querySelectorAll('.past-trip-row');

    function changePastTrips(direction) {
        pastTripRows[currentPastTripRow].classList.remove('active');
        currentPastTripRow += direction;
        if (currentPastTripRow < 0) {
            currentPastTripRow = pastTripRows.length - 1;
        } else if (currentPastTripRow >= pastTripRows.length) {
            currentPastTripRow = 0;
        }
        pastTripRows[currentPastTripRow].classList.add('active');
    }
</script>
