<?php include 'availability.php'; // Include the availability script
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/model.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>
        <?php echo isset($tripData['tripTitle']) ? htmlspecialchars($tripData['tripTitle']) : "Trip Details"; ?>
    </title>
</head>

<body>
    <?php include 'Header.php' ?>
    <div style="margin-top: 2500px; margin-left: 0">

        <?php

        // Include the existing database connection from UDB.php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);



        if (isset($_GET['trip_id'])) {
            $tripId = $_GET['trip_id'];
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            //$userId = 25;
            $mysqli = require __DIR__ . "/UDB.php";


            $sql = "SELECT * FROM trips WHERE tripId = $tripId";
            $result = $mysqli->query($sql);

            // Check if the user is in the booking list for this trip
            $checkBookingSql = "SELECT * FROM bookings WHERE userId = $userId AND tripId = $tripId";
            $bookingResult = $mysqli->query($checkBookingSql);

            $checkReviewSql = "SELECT * FROM reviews WHERE userId = $userId AND tripId = $tripId";
            $reviewResult = $mysqli->query($checkReviewSql);



            if ($result->num_rows > 0) {
                $tripData = $result->fetch_assoc();
                $tripDate = $tripData['tripDate'];
            }

            // Check if $tripData is not null
            if ($tripData !== null) {
                // Check if the trip date is valid before comparing
                if (!empty($tripData['tripDate'])) {
                    // Convert trip date to a timestamp
                    $tripDate = strtotime($tripData['tripDate']);
                    // Get the current date as a timestamp
                    $currentDate = strtotime(date("Y-m-d"));

                    if (!empty($tripData['tripDate'])) {
                        // Convert trip date to a timestamp
                        $tripDate = strtotime($tripData['tripDate']);
                        // Get the current date as a timestamp
                        $currentDate = strtotime(date("Y-m-d"));

                        // Check if the trip date is later than today
                        if ($tripDate < $currentDate) {
                            echo "<div>";
                            if ($bookingResult->num_rows > 0 && $reviewResult->num_rows ==0) {
                            echo "<center><button id='reviewaddbutton' onclick='openModal()'><i class='fa fa-plus'></i>&nbsp;&nbsp;Write a review</button></center><br><br>";
                            }
                            echo "</div>";
                        ?>
                            <br><br>
                            <div id="customerReviews">
                                <h4>Reviews</h4>
                                <?php include('DisplayTripReviews.php'); ?>
                            </div>

                        <?php
                                        }
                                    }
                                }
                            }
                        }
                        ?><!-- Modal -->
                        <div id="imageModal" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeImageModal()">&times;</span>

                                <img src="" id="preview-img">
                            </div>
                        </div>
                        <div id="myModal" class="modal">
                        <center>
                            <div class="modal-content">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <h4 style="float: left;"> Write a Review </h4>
                                <form id="reviewForm" enctype="multipart/form-data" action="AddReview.php?trip_id=<?php echo $_GET['trip_id']; ?>" method="POST">
                                <span id="error-message" style="color: red; font-family: \'Courier New\', Courier, monospace; font-weight: bold;"></span>
                                <br><br>
                                    <label for="review" style="float: left;">How was the trip?</label><br>
                                    <textarea id="review" name="review" rows="4" cols="50" required></textarea><br><br>

                                    <div class="rating">
                                        <label for="rating" style="float: left;">Rate the trip:</label>
                                        <div class="stars stars_rev">
                                            <input type="radio" name="rating" id="star1" value="5" required><label for="star1"></label>
                                            <input type="radio" name="rating" id="star2" value="4"><label for="star2"></label>
                                            <input type="radio" name="rating" id="star3" value="3"><label for="star3"></label>
                                            <input type="radio" name="rating" id="star4" value="2"><label for="star4"></label>
                                            <input type="radio" name="rating" id="star5" value="1"><label for="star5"></label>
                                        </div>
                                    </div>
                                    <br>
                                    <label for="pictures" style="float: left;">Share your captured pictures:<span style="color: grey;">&nbsp;&nbsp;(Optional)</span></label>
                                    <br><br>
                                    <input type="file" id="pictures" name="pictures[]" multiple accept="image/*">
                                    <br><br><br>
                                    <input class="submitreviewbtn" type="submit" value="Submit" onclick="submitReview()">
                                </form>
                            </div>
                        </center>
                        </div>


                        <script>
                            // Get the modal and button elements
                            var modal = document.getElementById("myModal");
                            var button = document.getElementById("addReviewButton");

                            // Function to open the modal
                            function openModal() {
                                modal.style.display = "block";
                                document.getElementById("error-message").textContent = ""; // Clear any previous error messages
                            }



                            function openModal() {
                                var modal = document.getElementById("myModal");
                                modal.style.display = "block";
                                document.body.style.overflow = 'hidden'; // Prevent scrolling
                            }

                            function closeModal() {
                                var modal = document.getElementById("myModal");
                                modal.style.display = "none";
                                document.body.style.overflow = ''; // Allow scrolling

                                // Reset the input fields and ratings
                                var reviewForm = document.getElementById("reviewForm");
                                reviewForm.reset();

                                // Reset the rating stars
                                var stars = document.querySelectorAll('.stars input[type="radio"]');
                                for (var i = 0; i < stars.length; i++) {
                                    stars[i].checked = false;
                                }

                                // Reset the image preview
                                document.getElementById("preview-img").src = "";

                                // Clear the error message
                                document.getElementById("error-message").textContent = "";
                            }

                            // If you want to close the modal when clicking outside of it
                            window.onclick = function(event) {
                                var modal = document.getElementById("myModal");
                                if (event.target === modal) {
                                    closeModal();
                                }
                            }

                            // image model

                            var imageModal = document.getElementById("imageModal");
                            var images = document.getElementsByClassName("review-img");

                            for (var i = 0; i < images.length; i++) {
                                images[i].addEventListener('click', function() {
                                    imageModal.style.display = "block";
                                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                                    document.getElementById("preview-img").setAttribute('src', this.getAttribute('src'));
                                }, false);
                            }

                            function closeImageModal() {
                                imageModal.style.display = "none";
                                document.body.style.overflow = ''; // Allow scrolling
                            }

                            // Close the modal if clicked outside the image
                            imageModal.addEventListener('click', function(e) {
                                if (e.target === imageModal) {
                                    closeImageModal();
                                }
                            });

                            function submitReview() {
                                var reviewText = document.getElementById("review").value;
                                var rating = document.querySelector('input[name="rating"]:checked');

                                if (!reviewText && !rating) {
                                    document.getElementById("error-message").textContent = "Please fill in the review and rating.";
                                } else if (!reviewText) {
                                    document.getElementById("error-message").textContent = "Please fill in the review.";
                                } else if (!rating) {
                                    document.getElementById("error-message").textContent = "Please fill in the rating.";
                                } else {
                                    document.getElementById("error-message").textContent = ""; // Clear any previous error messages
                                    document.getElementById("reviewForm").submit(); // Submit the form if all fields are filled
                                }
                            }
                        </script>

    </div>
    <div id="containtrip" style="background-color: white; height: 650px; margin-top: 80%;">
        <button class="copyurlbutton" onclick="copyUrl(); playAudio();">&nbsp;<i class="fa fa-share-alt-square fa-3x" aria-hidden="true"></i></button>
        <?php
        // Include the existing database connection from UDB.php
        $mysqli = require __DIR__ . "/UDB.php";

        // Get the trip ID from the query parameters
        if (isset($_GET['trip_id'])) {
            $tripId = $_GET['trip_id'];

            // Prepare and execute the SQL query to retrieve trip details
            $query = "SELECT * FROM trips WHERE tripId = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("i", $tripId);
            $stmt->execute();
            $result = $stmt->get_result();


            // Check if the trip has available slots
            $tripQuery = "SELECT maxParticipants FROM trips WHERE tripId = ?";
            $tripStmt = $mysqli->prepare($tripQuery);
            $tripStmt->bind_param("i", $tripId);
            $tripStmt->execute();
            $tripStmt->store_result();

            if ($tripStmt->num_rows === 1) {
                $tripStmt->bind_result($maxParticipants);
                $tripStmt->fetch();

                // Check if the total participants after booking will exceed the maxParticipants
                $bookedParticipantsQuery = "SELECT SUM(participants) FROM bookings WHERE tripId = ?";
                $bookedParticipantsStmt = $mysqli->prepare($bookedParticipantsQuery);
                $bookedParticipantsStmt->bind_param("i", $tripId);
                $bookedParticipantsStmt->execute();
                $bookedParticipantsStmt->store_result();
                $bookedParticipantsStmt->bind_result($bookedParticipants);
                $bookedParticipantsStmt->fetch();

                $availableSlots = $maxParticipants - $bookedParticipants;
                $isBookable = $availableSlots > 0;
            }

            if ($result->num_rows > 0) {

                // Fetch the trip data
                $tripData = $result->fetch_assoc();

                // Fetch location name based on tripLocation ID
                $locationQuery = "SELECT locationName FROM locations WHERE id = ?";
                $locationStmt = $mysqli->prepare($locationQuery);
                $locationStmt->bind_param("i", $tripData['tripLocation']);
                $locationStmt->execute();
                $locationResult = $locationStmt->get_result();

                if ($locationResult->num_rows > 0) {
                    $locationData = $locationResult->fetch_assoc();
                    $locationName = $locationData['locationName'];
                } else {
                    $locationName = "Unknown Location";
                }

                // Button to Switch to Content 2
                echo "<button id='switchButton' onclick='switchContent()'>
                <i class='fa fa-chevron-left'></i></button>";
                // Button to Switch to Content 2
                echo "<button id='backButton' onclick='switchContent()'>
                <i class='fa fa-chevron-right'></i></button>";

                // Content 1

                echo "<div id='content1' class='content'>";

                echo "<h2 class='triptitle' style=\'font-family: Candara, Calibri, Segoe, Segoe UI, Optima, Arial, sans-serif;\'><span>{$tripData['tripTitle']}</span></h2>";
                echo "<div class='rowtrip' style='background-color: white;'>";
                echo "<br>";

                echo "<div class='columntrip' id='leftboxtrip'>";
                echo "<i class='fa fa-map-marker fa-lg circle-icon-location' style='background-color: #c76377; color: #4f0f1c;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$locationName}</p>";
                echo "</div>";

                echo "<div class='columntrip' id='middleboxtrip'>";
                echo "<i class='fa fa-calendar fa-lg circle-icon-date' style='background-color: #77a0a3; color: #2c4a4d;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$tripData['tripDate']}</p>";
                echo "</div>";

                echo "<div class='columntrip' id='middleboxtrip2'>";
                echo "<i class='fa fa-clock-o fa-lg circle-icon-time' style='background-color: #edb677; color: #6e4619;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$tripData['tripTime']}</p>";
                echo "</div>";

                echo "<div class='columntrip' id='middleboxtrip3'>";
                echo "<i class='fa fa-ils fa-lg circle-icon-price' style='background-color: #9bbf6b; color: #496821;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$tripData['tripPrice']}</p>";
                echo "</div>";

                echo "<div class='columntrip' id='rightboxtrip'>";
                echo "<i class='fa fa-users fa-lg circle-icon-maxparti' style='background-color: #7d536c; color: #532541;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$tripData['maxParticipants']}</p>";
                echo "</div>";

                echo "<hr>";

                echo "<br>";

                echo "<div class='itineraries' style=\"font-weight: bold; padding-left: 100px; float:left; \">Our Itineraries:</div>";
                echo "<br>";
                echo "<br>";
                echo "<div class='itineraries' style=\"text-align: left; padding-left: 150px;\">";

                // Display itineraries from the database
                if (!empty($tripData['tripItineraries'])) {
                    // Explode itineraries into an array
                    $itinerariesArray = explode("\n", $tripData['tripItineraries']);

                    // Loop through the array and add the icon to each line
                    foreach ($itinerariesArray as $itinerary) {
                        echo "<i class='fa fa-map-marker'></i>&nbsp;&nbsp;&nbsp; $itinerary<br>";
                    }
                } else {
                    echo "<p>No itineraries available for this trip.</p>";
                }

                echo "</div>";


                if (isset($_SESSION['user_id'])) {
                    // User is logged in, render the buttons
                    echo "<div class='booktrip-and-wishlist-buttons'>";
                    echo "<br><br><br>";

                    if ($isBookable) {
                        echo "<a href='BookTrip_FrontEnd.php?trip_id=$tripId' style='text-decoration: none; padding-right: 20px; padding-left: 20px;'>";
                        echo "<button class='booktripbutton' onclick='playAudio()'>Book The Trip</button>";
                        echo "</a>";

                        echo "<a href='javascript:void(0);' onclick='toggleWishlist({$tripId})'>";
                        echo "<i class='fa fa-heart fa-2x wishlist-icon' data-trip-id='{$tripId}' style='padding-right: 20px; padding-left: 20px;'></i>";
                        echo "</a>";

                        echo "<a href='javascript:void(0);'  style='pointer-events: none; display: none;'>";
                        echo "<i class='fa fa-hourglass fa-2x waiting-icon' data-trip-id='{$tripId}'></i>";
                        echo "</a>";
                    } else {
                        echo "<button class='booktripbutton' style='background-color: grey; cursor: not-allowed; padding-right: 20px; padding-left: 20px;' disabled>Booking Full</button>";

                        echo "<a href='javascript:void(0);' onclick='toggleWishlist({$tripId})'>";
                        echo "<i class='fa fa-heart fa-2x wishlist-icon' data-trip-id='{$tripId}' style='padding-right: 20px; padding-left: 20px;'></i>";
                        echo "</a>";

                        echo "<a href='javascript:void(0);' onclick='toggleWaitinglist({$tripId})'>";
                        echo "<i class='fa fa-hourglass fa-2x waiting-icon' data-trip-id='{$tripId}'></i>";
                        echo "</a>";
                    }


                    echo "<br>";
                    echo "</div>";

                    // Get the current date and time
                    $currentTimestamp = strtotime(date("Y-m-d H:i:s"));

                    // Check if the trip date is in the future (upcoming)
                    if (strtotime($tripData['tripDate'] . ' ' . $tripData['tripTime']) > $currentTimestamp) {
                        // Calculate available spots
                        $availableSpots = $maxParticipants - $totalParticipants;

                        // Display the availability message with different colors based on the available spots
                        if ($availableSpots == 0) {
                            echo "<p style='color: red; font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>This trip is fully booked.</p>";
                        } elseif ($availableSpots == 1) {
                            echo "<p style='color: red; font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>Only 1 spot left!</p>";
                        } elseif ($availableSpots <= 5) {
                            echo "<p style='color: red; font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>Only {$availableSpots} spots left!</p>";
                        } else {
                            echo "<p style='color: black; font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>{$availableSpots} available spots.</p>";
                        }
                    } else {
                        // This is not an upcoming trip, you can display a message indicating that it's in the past
                        echo "<p style='color: gray; font-weight: bold; font-family: \"Courier New\", Courier, monospace;''>This trip has already taken place.</p>";
                    }
                } else {
                    // User is not logged in, disable the buttons and show a message
                    echo "<div class='booktrip-and-wishlist-buttons'>";
                    echo "<br><br><br>";
                    echo "<a href='javascript:void(0);' style='text-decoration: none; padding-right: 20px; padding-left: 20px;'>";
                    echo "<button class='booktripbutton' disabled style=\"background-color: grey; cursor: inherit;\">Book The Trip</button>";
                    echo "</a>";
                    echo "<a href='javascript:void(0);' style='pointer-events: none;'>";
                    echo "<i class='fa fa-heart fa-2x wishlist-icon' style='color: gray;'></i>";
                    echo "</a>";
                    echo "<a href='javascript:void(0);' style='pointer-events: none;'>";
                    echo "<i class='fa fa-hourglass fa-2x waiting-icon' style='color: gray;'></i>";
                    echo "</a>";
                    echo "<br>";
                    echo "</div>";
                    echo "<p style='font-size: 14px; text-align: center; margin-top: 10px;'>Please log in to book, add to wishlist, or join the waiting list.</p>";
                    // Get the current date and time
                    $currentTimestamp = strtotime(date("Y-m-d H:i:s"));

                    // Check if the trip date is in the future (upcoming)
                    if (strtotime($tripData['tripDate'] . ' ' . $tripData['tripTime']) > $currentTimestamp) {
                        // Calculate available spots
                        $availableSpots = $maxParticipants - $totalParticipants;

                        // Display the availability message with different colors based on the available spots
                        if ($availableSpots == 0) {
                            echo "<p style='color: red; font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>This trip is fully booked.</p>";
                        } elseif ($availableSpots == 1) {
                            echo "<p style='color: red; font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>Only 1 spot left!</p>";
                        } elseif ($availableSpots <= 5) {
                            echo "<p style='color: red;  font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>Only {$availableSpots} spots left!</p>";
                        } else {
                            echo "<p style='color: black; font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>{$availableSpots} available spots.</p>";
                        }
                    } else {
                        // This is not an upcoming trip, you can display a message indicating that it's in the past
                        echo "<p style='color: gray; font-weight: bold; font-family: \"Courier New\", Courier, monospace;'>This trip has already taken place.</p>";
                    }
                }

                // Closure Content 1
                echo "</div>";
            } else {
                echo "<p>No trip found with the given ID.</p>";
            }
        } else {
            echo "<p>Trip ID not provided.</p>";
        }
        ?>
    </div>

    <!-- Content 2 -->
    <div id='content2' class='content'>
        <br><br>
        <p id='tripinfo' style="padding-top:10px; font-weight: bold; font-family: Candara, Calibri, Segoe, Segoe UI, Optima, Arial, sans-serif;">
            <i class="fa fa-info-circle fa-1x" style="background-color: white; color: #77b4ca;"></i>
            &nbsp;Some information about our trip destination:
        </p>
        <br><br><br>
        <form method="get" id="fetchForm">
            <?php
            // Get the trip ID from the query parameters
            if (isset($_GET['trip_id'])) {
                $tripId = $_GET['trip_id'];

                // Prepare and execute the SQL query to retrieve trip details
                $query = "SELECT * FROM trips WHERE tripId = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("i", $tripId);
                $stmt->execute();
                $result = $stmt->get_result();

                // Fetch the trip data
                $tripData = $result->fetch_assoc();

                // Fetch the trip title from the tripData
                $tripTitleFromDbForInput = $tripData['tripTitle'];

                echo "<input type='hidden' name='trip_id' value='{$tripId}'>";
            }
            ?>
        </form>
        <br><br>
        <div class='tripinformation' style="float: left; padding-left: 170px; padding-right: 170px;">
            <?php
            if (isset($_GET['tripTitle'])) {
                $tripId = $_GET['trip_id'];

                $result = $stmt->get_result();
                $tripTitle = $_GET['tripTitle'];
                $tripTitleWithUnderscores = str_replace(' ', '_', $tripTitle);

                $encodedTripTitle = urlencode($tripTitleWithUnderscores);
                $wikiApiUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/{$encodedTripTitle}";

                $response = file_get_contents($wikiApiUrl);
                $data = json_decode($response, true);

                if (isset($data['title'])) {
                    echo "<div style='display: flex; align-items: flex-start;'>"; // Flex container starts

                    // Display the image
                    if (isset($data['thumbnail']['source'])) {
                        $imageSource = $data['thumbnail']['source'];
                        echo "<div style='width: 200px; height: 200px; overflow: hidden; margin-right: 40px; margin-top: 45px; border-radius: 1px; box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.5);'>";
                        echo "<img src='{$imageSource}' alt='Image not available' style='width: 100%; height: auto;'>";
                        echo "</div>";
                    }

                    // Extract the image URL and store it in a session variable
                    $_SESSION['trip_image_url'] = isset($data['thumbnail']['source']) ? $data['thumbnail']['source'] : '';

                    echo "<div style='flex: 1; font-family: Candara, Calibri, Segoe, Segoe UI, Optima, Arial, sans-serif;'>"; // Div for title and description starts
                    echo "<h2>{$data['title']}</h2>";
                    echo "<br>";
                    if (isset($data['extract'])) {
                        echo "<p>{$data['extract']}</p>";
                    } else {
                        echo "<p>No information available for this trip title.</p>";
                    }
                    echo "</div>"; // Div for title and description ends

                    echo "</div>"; // Flex container ends
                } else {
                    echo "<p>No information available for this trip title.</p>";
                }
            }
            ?>
            <br><br>
            <p style="font-weight: bold; font-family: 'Baloo 2', cursive;">🏞️ Let's Discover
                <?php echo isset($tripData['tripTitle']) ? htmlspecialchars($tripData['tripTitle']) : "the Destination"; ?>
                - Your Adventure Awaits! 🌄🗺️</p>
        </div>
        <div class="unvisiblediv" style="margin-top: 100px;">
            <h2>&nbsp;&nbsp;&nbsp;</h2>
        </div>
    </div>
    <div class="map-weather-container" style="margin-left: 0">
        <?php include 'Feature_InteractiveMap.php'; ?>
        <?php include 'Feature_WeatherForecast.php'; ?>
    </div>
    <div class="unvisiblediv" style="margin-top: 100px;">
        <h2>&nbsp;&nbsp;&nbsp;</h2>
    </div>
</body>

</html>

<!-- Copy URL Button -->
<script>
    function copyUrl() {
        var url = window.location.href; // get the current URL
        navigator.clipboard.writeText(url); // copy the URL to the clipboard
        alert("URL copied to clipboard!");
    }
</script>

<!-- Button Audio -->
<script>
    function playAudio() {
        var audio = document.getElementById("myAudio");
        audio.play();
    }
</script>

<!-- Wishlist Audio -->
<script>
    function playAudioWishlist() {
        var audio = document.getElementById("myAudioWishlist");
        audio.play();
    }
</script>

<script>
    // Function to switch between content 1 and content 2
    function switchContent() {
        const content1 = document.getElementById("content1");
        const content2 = document.getElementById("content2");
        const tripinfo = document.getElementById("tripinfo");
        const switchButton = document.getElementById("switchButton");
        const backButton = document.getElementById("backButton");

        if (content1.style.display === "none") {

            content1.style.display = "block";
            content2.style.display = "none";
            tripinfo.style.display = "none";

        } else {

            content1.style.display = "none";
            content2.style.display = "block";
            tripinfo.style.display = "block";

            // Auto-submit the form on page load
            window.addEventListener('load', function() {
                document.getElementById('autoFetchForm').submit();
            });
        }
    }
</script>

<script>
    // Set user ID in session (assuming you have a way to authenticate users)
    <?php
    if (isset($_SESSION['user_id'])) {
        echo "const userId = " . $_SESSION['user_id'] . ";";
    } else {
        echo "const userId = null;";
    }
    ?>

    // Wishlist Toggle
    let wishlistClicked = sessionStorage.getItem('wishlistClicked') === 'true'; // Retrieve state from session storage

    // JavaScript code
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize wishlist icon states for all trips
        const wishlistIcons = document.querySelectorAll('.wishlist-icon');
        wishlistIcons.forEach(icon => {
            const tripId = icon.getAttribute('data-trip-id');
            const isWishlistClicked = sessionStorage.getItem(`wishlistClicked_${tripId}`) === 'true';
            if (isWishlistClicked) {
                icon.classList.add('clicked');
                icon.style.color = 'pink';
            }
        });
    });

    function toggleWishlist(tripId) {
        const heartIcon = document.querySelector(`.wishlist-icon[data-trip-id="${tripId}"]`);
        if (userId !== null) {
            const isWishlistClicked = sessionStorage.getItem(`wishlistClicked_${tripId}`) === 'true';
            if (isWishlistClicked) {
                heartIcon.classList.remove('clicked');
                heartIcon.style.color = 'rgb(190, 190, 190)';
                removeFromWishlist(tripId);
            } else {
                heartIcon.classList.add('clicked');
                heartIcon.style.color = 'pink';
                addToWishlist(tripId);
            }
            sessionStorage.setItem(`wishlistClicked_${tripId}`, !isWishlistClicked); // Store state in session storage
        } else {
            // Handle when the user is not logged in
            // You can show an alert or redirect to a login page
        }
    }

    // Add to Wishlist
    function addToWishlist(tripId) {
        const formData = new FormData();
        formData.append('trip_id', tripId);
        fetch('Wishlist_Add_BackEnd.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
    }

    // Remove from Wishlist
    function removeFromWishlist(tripId) {
        const formData = new FormData();
        formData.append('trip_id', tripId);
        fetch('Wishlist_Remove_BackEnd.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
    }


    // Waitinglist Toggle
    let waitinglistClicked = sessionStorage.getItem('waitinglistClicked') === 'true'; // Retrieve state from session storage

    // JavaScript code
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize waitinglist icon states for all trips
        const waitinglistIcons = document.querySelectorAll('.waiting-icon');
        waitinglistIcons.forEach(icon => {
            const tripId = icon.getAttribute('data-trip-id');
            const isWaitinglistClicked = sessionStorage.getItem(`waitinglistClicked_${tripId}`) === 'true';
            if (isWaitinglistClicked) {
                icon.classList.add('clicked');
                icon.style.color = '#9bbf6b';
            }
        });
    });

    function toggleWaitinglist(tripId) {
        const hourGlassIcon = document.querySelector(`.waiting-icon[data-trip-id="${tripId}"]`);
        if (userId !== null) {
            const isWaitinglistClicked = sessionStorage.getItem(`waitinglistClicked_${tripId}`) === 'true';
            if (isWaitinglistClicked) {
                hourGlassIcon.classList.remove('clicked');
                hourGlassIcon.style.color = 'rgb(190, 190, 190)';
                removeFromWaitinglist(tripId);
            } else {
                hourGlassIcon.classList.add('clicked');
                hourGlassIcon.style.color = '#9bbf6b';
                addToWaitinglist(tripId);
            }
            sessionStorage.setItem(`waitinglistClicked_${tripId}`, !isWaitinglistClicked); // Store state in session storage
        } else {
            // Handle when the user is not logged in
            // You can show an alert or redirect to a login page
        }
    }

    // Add to Waitinglist
    function addToWaitinglist(tripId) {
        const formData = new FormData();
        formData.append('trip_id', tripId);
        fetch('WaitingList_Add_BackEnd.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
    }

    // Remove from Waitinglist
    function removeFromWaitinglist(tripId) {
        const formData = new FormData();
        formData.append('trip_id', tripId);
        fetch('WaitingList_Remove_BackEnd.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize wishlist icon states for all trips
        const wishlistIcons = document.querySelectorAll('.wishlist-icon');
        wishlistIcons.forEach(icon => {
            const tripId = icon.getAttribute('data-trip-id');
            const isWishlistClicked = sessionStorage.getItem(`wishlistClicked_${tripId}`) === 'true';
            if (isWishlistClicked) {
                icon.classList.add('clicked');
                icon.style.color = 'pink';
            }
        });

        // Fetch the user's wishlist
        fetch('Wishlist_Fetch.php')
            .then(response => response.json())
            .then(wishlist => {
                // Check if the current trip is in the wishlist
                const currentTripId = <?php echo $tripId; ?>;
                if (wishlist.includes(currentTripId)) {
                    // Set the button color to pink
                    const heartIcon = document.querySelector(`.wishlist-icon[data-trip-id="${currentTripId}"]`);
                    heartIcon.style.color = 'pink';
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize waiting list icon states for all trips
        const waitingIcons = document.querySelectorAll('.waiting-icon');
        waitingIcons.forEach(icon => {
            const tripId = icon.getAttribute('data-trip-id');
            const isWaitingClicked = sessionStorage.getItem(`waitingClicked_${tripId}`) === 'true';
            if (isWaitingClicked) {
                icon.classList.add('clicked');
                icon.style.color = '#9bbf6b';
            }
        });

        // Fetch the user's waiting list
        fetch('WaitingList_Fetch.php')
            .then(response => response.json())
            .then(waitinglist => {
                // Check if the current trip is in the waiting list
                const currentTripId = <?php echo $tripId; ?>;
                if (waitinglist.includes(currentTripId)) {
                    // Set the button color to green
                    const hourGlassIcon = document.querySelector(`.waiting-icon[data-trip-id="${currentTripId}"]`);
                    hourGlassIcon.style.color = '#9bbf6b';
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>