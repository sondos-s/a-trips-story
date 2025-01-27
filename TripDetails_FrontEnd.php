<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/weather.css">
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

    <style>
        #map-canvas {
            /* margin-top: 1000px; */
            background-color: white;
            visibility: visible;
            width: 50%;
            height: 400px;
            position: fixed;
            left: -5px;
            top: 100px;
        }

        #weather-info-sec {

            position: absolute;
            left: 700px;
            top: 700px;
        }
    </style>

</head>

<body>
    <?php include 'Header.php' ?>
    <div style="margin-top: 3000px; margin-left: 0">
        <?php

        // Include the existing database connection from UDB.php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);



        if (isset($_GET['trip_id'])) {
            $tripId = $_GET['trip_id'];
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

            $mysqli = require __DIR__ . "/UDB.php";


            $sql = "SELECT * FROM trips WHERE tripId = $tripId";
            $result = $mysqli->query($sql);

            if ($result->num_rows > 0) {
                $tripData = $result->fetch_assoc();
                $tripDate = $tripData['tripDate'];
            }

            // Check if $tripData is not null
            if ($tripData !== null) {

                // Check if the user is in the booking list for this trip
                $checkBookingSql = "SELECT * FROM bookings WHERE userId = $userId AND tripId = $tripId";
                $bookingResult = $mysqli->query($checkBookingSql);

                $checkReviewSql = "SELECT * FROM reviews WHERE userId = $userId AND tripId = $tripId";
                $reviewResult = $mysqli->query($checkReviewSql);

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
                            if ($bookingResult->num_rows > 0) {

                                if ($reviewResult->num_rows == 0) {
                                    echo "<center><button id='addReviewButton' class='booktripbutton' onclick='openModal()'>add review</button></center><br><br>";
                                }
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
                <span class="close" onclick="closeImageModal()">&times;</span> <!-- Add onclick attribute here -->

                <img src="" id="preview-img">
            </div>
        </div>
        <div id="myModal" class="modal">
            <center>
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span> <!-- Add onclick attribute here -->
                    <form id="reviewForm" enctype="multipart/form-data" action="AddReview.php?trip_id=<?php echo $_GET['trip_id']; ?>" method="POST">
                        <label for="review">How was the trip?</label><br>
                        <textarea id="review" name="review" rows="4" cols="50" required></textarea><br><br>

                        <div class="rating">
                            <label for="rating">Rate the trip:</label><br>
                            <div class="stars stars_rev">
                                <input type="radio" name="rating" id="star1" value="5" required><label for="star1"></label>
                                <input type="radio" name="rating" id="star2" value="4"><label for="star2"></label>
                                <input type="radio" name="rating" id="star3" value="3"><label for="star3"></label>
                                <input type="radio" name="rating" id="star4" value="2"><label for="star4"></label>
                                <input type="radio" name="rating" id="star5" value="1"><label for="star5"></label>
                            </div>
                        </div><br>

                        <label for="pictures">Upload Pictures: <span class="optional-text">(Optional)</span></label><br>
                        <input type="file" id="pictures" name="pictures[]" multiple accept="image/*"><br><br>


                        <input type="submit" value="Add review">
                    </form>
                </div>
            </center>
        </div>

        <script type="text/javascript">
            document.getElementById('reviewForm').addEventListener('submit', function(e) {
                let radios = document.getElementsByName('rating');
                let formValid = false;

                let i = 0;
                while (!formValid && i < radios.length) {
                    if (radios[i].checked) formValid = true;
                    i++;
                }

                if (!formValid) {
                    alert('Please rate the trip.');
                    e.preventDefault();
                }
            });
        </script>



        <script>
            // Get the modal and button elements
            var modal = document.getElementById("myModal");
            var button = document.getElementById("addReviewButton");

            // Function to open the modal
            function openModal() {
                document.getElementById('myModal').style.display = 'block';
                document.body.style.overflow = 'hidden'; // Add this line
            }



            // Function to close the modal
            function closeModal() {
                document.getElementById('myModal').style.display = 'none';
                document.body.style.overflow = ''; // Add this line
            }

            // Event listener for the button click
            button.addEventListener("click", openModal);

            // Event listener to close the modal if the user clicks outside of it
            window.addEventListener("click", function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            });



            // image model

            var imageModal = document.getElementById("imageModal");

            var images = document.getElementsByClassName("review-img");

            for (var i = 0; i < images.length; i++) {
                images[i].addEventListener('click', function() {
                    imageModal.style.display = "block";
                    console.log(this.getAttribute('src'));

                    document.getElementById("preview-img").setAttribute('src', this.getAttribute('src'));
                }, false);
            }

            function closeImageModal() {


                imageModal.style.display = "none";
            }
        </script>
    </div>
    <!-- <button id='switchButton' onclick='switchContent()'>
        <i class='fa fa-chevron-left'></i>
    </button>
    <button id='backButton' onclick='switchContent()'>
        <i class='fa fa-chevron-right'></i>
    </button>-->
    <div id="containtrip" style="background-color: white; height: 700px; margin-top: 90%;">
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

                    // Use Google Maps Geocoding API to get coordinates
                    $apiKey = 'AIzaSyApmIjM2bI6swuqNlvPQTOaI0wyhGk_uc8';
                    $encodedLocationName = urlencode($locationName);
                    $geocodingUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedLocationName}&key={$apiKey}";

                    // Make the API request
                    $geocodingResponse = file_get_contents($geocodingUrl);
                    $geocodingData = json_decode($geocodingResponse, true);

                    if ($geocodingData['status'] === 'OK' && isset($geocodingData['results'][0]['geometry']['location'])) {
                        $latitude = $geocodingData['results'][0]['geometry']['location']['lat'];
                        $longitude = $geocodingData['results'][0]['geometry']['location']['lng'];

                        // the latitude and longitude for the location to use these values in your map initialization code

                    } else {
                        // Handle the case where geocoding failed
                        $latitude = 0; // Set default values
                        $longitude = 0;
                    }
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


                echo "</div>";


                if (isset($_SESSION['user_id'])) {
                    // User is logged in, render the buttons
                    echo "<div class='booktrip-and-wishlist-buttons'>";
                    echo "<br><br><br>";
                    echo "<a href='BookTrip_FrontEnd.php?trip_id=$tripId' style='text-decoration: none; padding-right: 20px; padding-left: 20px;'>";
                    echo "<button class='booktripbutton' onclick='playAudio()'>Book The Trip</button>";
                    echo "</a>";
                    echo "<a href='javascript:void(0);' onclick='toggleWishlist()'>";
                    echo "<i class='fa fa-heart fa-2x wishlist-icon'></i>";
                    echo "</a>";
                    echo "<a href='javascript:void(0);' onclick='toggleWaitinglist()'>";
                    echo "<i class='fa fa-hourglass fa-2x waiting-icon'></i>";
                    echo "</a>";
                    echo "<br>";
                    echo "</div>";
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
                }

                // Closure Content 1
                echo "</div>";
            } else {
                echo "<p>No trip found with the given ID.</p>";
            }
        } else {
            echo "<p>Trip ID not provided.</p>";
        }
        //////////////////// weather starts here 
        // Your API key from OpenWeatherMap

        $apiKey = "3045dd712ffe6e702e3245525ac7fa38";



        // API endpoint URL for OpenWeatherMap with your API key
        $apiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$apiKey}";

        // Make the API request and get the response
        $weatherData = file_get_contents($apiUrl);

        // Check if the request was successful
        if ($weatherData) {
            // Parse the JSON response
            $weatherInfo = json_decode($weatherData, true);

            // Extract relevant weather information
            $temperatureKelvin = $weatherInfo['main']['temp'];
            $temperatureCelsius = round($temperatureKelvin - 273.15); // Convert from Kelvin to Celsius and round it
            $description = $weatherInfo['weather'][0]['description'];

            // Create a div to display weather information
            echo "<div id='weather-info-sec'>";
            echo " <article class='box weather'>";
            echo "<div class='icon bubble black'>";
            echo "<div class='spin'>";
            echo "<img src='https://dl.dropbox.com/s/0qq5anxliaopt8d/sun.png'>";
            echo "</div>";
            echo "</div>";

            echo "<h3>Weather Information for {$locationName}</h3>"; // Use the trip location
            echo "<p class='temp'><strong>Temperature:</strong> " . number_format($temperatureCelsius, 2) . "°C</p>"; // Format temperature to 2 decimal places
            echo "<p><strong>Description:</strong> " . $description . "</p>";
        } else {
            // Handle the case where the API request failed
            echo "Failed to retrieve weather data.";
        }

        echo "</article>";
        echo "</div>";

        ?>

        <script>
            // Define the initMap function in the global scope
            function initMap() {
                console.log("Initializing the map...");
                // Get the latitude and longitude values from your PHP variables
                var latitude = <?php echo $latitude; ?>;
                var longitude = <?php echo $longitude; ?>;

                // Create a LatLng object
                var location = new google.maps.LatLng(latitude, longitude);

                // Map options
                var mapOptions = {
                    zoom: 12, // You can adjust the initial zoom level
                    center: location, // Center the map on the specified location
                };

                // Create the map
                var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                // Create a marker for the location
                var marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: 'Trip Location', // You can set a custom title for the marker
                });
            }
        </script>
        <br><br><br><br><br>
        <!-- Load the Google Maps API with your API key -->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyApmIjM2bI6swuqNlvPQTOaI0wyhGk_uc8&libraries&callback=initMap" async defer></script>
        <!------------------------------------------------------------------------>
        <div id="map-canvas"></div>
        <!------------------------------------------------------------------------>
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
        <div>
        </div>
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
            <p style="font-weight: bold; font-family: 'Baloo 2', cursive;">
                🏞 Let's Discover
                <?php echo isset($tripData['tripTitle']) ? htmlspecialchars($tripData['tripTitle']) : "the Destination"; ?>
                - Your Adventure Awaits! 🌄🗺
            </p>
        </div>
        <div class="unvisiblediv" style="margin-top: 100px;">
            <h2>&nbsp;&nbsp;&nbsp;</h2>
        </div>
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

    function toggleWishlist() {
        const heartIcon = document.querySelector('.wishlist-icon');
        if (userId !== null) {
            if (wishlistClicked) {
                heartIcon.classList.remove('clicked');
                heartIcon.style.color = 'rgb(190, 190, 190)';
                removeFromWishlist();
            } else {
                heartIcon.classList.add('clicked');
                heartIcon.style.color = 'pink';
                addToWishlist();
            }
            wishlistClicked = !wishlistClicked;
            sessionStorage.setItem('wishlistClicked', wishlistClicked); // Store state in session storage
        } else {
            // Handle when the user is not logged in
            // You can show an alert or redirect to a login page
        }
    }

    // Add to Wishlist
    function addToWishlist() {
        const tripId = <?php echo $tripId; ?>;
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
    function removeFromWishlist() {
        const tripId = <?php echo $tripId; ?>;
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

    function toggleWaitinglist() {
        const hourGlassIcon = document.querySelector('.waiting-icon');
        if (userId !== null) {
            if (waitinglistClicked) {
                hourGlassIcon.classList.remove('clicked');
                hourGlassIcon.style.color = 'rgb(190, 190, 190)';
                removeFromWaitinglist();
            } else {
                hourGlassIcon.classList.add('clicked');
                hourGlassIcon.style.color = '#9bbf6b';
                addToWaitinglist();
            }
            waitinglistClicked = !waitinglistClicked;
            sessionStorage.setItem('waitinglistClicked', waitinglistClicked); // Store state in session storage
        } else {
            // Handle when the user is not logged in
            // You can show an alert or redirect to a login page
        }
    }

    // Add to Waitinglist
    function addToWaitinglist() {
        const tripId = <?php echo $tripId; ?>;
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
    function removeFromWaitinglist() {
        const tripId = <?php echo $tripId; ?>;
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

    // DOMContentLoaded event listener
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize wishlist icon state
        const wishlistIcon = document.querySelector('.wishlist-icon');
        if (wishlistClicked) {
            wishlistIcon.classList.add('clicked');
            wishlistIcon.style.color = 'pink';
        }

        // Initialize waitinglist icon state
        const waitinglistIcon = document.querySelector('.waiting-icon');
        if (waitinglistClicked) {
            waitinglistIcon.classList.add('clicked');
            waitinglistIcon.style.color = '#9bbf6b';
        }
    });
</script>