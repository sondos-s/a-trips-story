<?php
// Include the existing database connection from UDB.php
$mysqli = require __DIR__ . "/UDB.php";

// Fetch location name based on tripLocation ID
$locationName = "";

if (isset($tripData['tripLocation'])) {
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
}

// Get latitude and longitude for the location
$latitude = 0;  // Set default latitude
$longitude = 0; // Set default longitude

if (!empty($locationName)) {
    $apiKey = 'AIzaSyApmIjM2bI6swuqNlvPQTOaI0wyhGk_uc8';
    $encodedLocationName = urlencode($locationName);
    $geocodingUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedLocationName}&key={$apiKey}";

    $geocodingResponse = file_get_contents($geocodingUrl);
    $geocodingData = json_decode($geocodingResponse, true);

    if ($geocodingData['status'] === 'OK' && isset($geocodingData['results'][0]['geometry']['location'])) {
        $latitude = $geocodingData['results'][0]['geometry']['location']['lat'];
        $longitude = $geocodingData['results'][0]['geometry']['location']['lng'];
    }
}
?>

<div id="interactivemap"></div>

<script>
    // Define the initMap function in the global scope
    function initMap() {
        console.log("Initializing the map...");
        // Get the latitude and longitude values from your PHP variables
        var latitude = <?php echo $latitude; ?>;
        var longitude = <?php echo $longitude; ?>;

        // LatLng object
        var location = new google.maps.LatLng(latitude, longitude);

        // Map options
        var mapOptions = {
            zoom: 12,
            center: location, // Center the map on the specified location
        };

        // Map creation
        var map = new google.maps.Map(document.getElementById('interactivemap'), mapOptions);

        // Marker for the location
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            title: 'Trip Location', // You can set a custom title for the marker
        });
    }

    
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyApmIjM2bI6swuqNlvPQTOaI0wyhGk_uc8&libraries&callback=initMap" async defer></script>


