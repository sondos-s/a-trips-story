<?php
    // Include the existing database connection from UDB.php
    $mysqli = require __DIR__ . "/UDB.php";

    // Retrieve all trips from the database
    $query = "SELECT * FROM trips";
    $result = $mysqli->query($query);

    // Prepare Wikipedia API requests in advance
    $apiRequests = [];
    while ($row = $result->fetch_assoc()) {
        $tripTitle = urlencode(str_replace(' ', '_', $row['tripTitle']));
        $apiRequests[$row['tripId']] = "https://en.wikipedia.org/api/rest_v1/page/summary/{$tripTitle}";
    }

    // Close the database connection
    $mysqli->close();

    // Initiate a multi-cURL request to fetch API responses concurrently
    $curlHandles = [];
    $mh = curl_multi_init();
    foreach ($apiRequests as $tripId => $apiUrl) {
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_multi_add_handle($mh, $ch);
        $curlHandles[$tripId] = $ch;
    }

    // Execute multi-cURL requests
    do {
        curl_multi_exec($mh, $running);
    } while ($running > 0);

    // Fetch and display trip details
    foreach ($curlHandles as $tripId => $ch) {
        $response = curl_multi_getcontent($ch);
        $data = json_decode($response, true);
        $imageUrl = isset($data['thumbnail']['source']) ? $data['thumbnail']['source'] : '';

        echo "<div style='display: flex; align-items: center;'>";
        echo "<img src='$imageUrl' alt='Trip Image' style='width: 200px; height: 200px; margin-right: 10px;'>";
        echo "<p><a href='TripDetails_FrontEnd.php?trip_id=$tripId&tripTitle=" . urlencode($data['title']) . "'>{$data['title']}</a></p>";
        echo "</div>";

        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    // Close the multi-cURL handle
    curl_multi_close($mh);
?>
<!DOCTYPE html>
<html>
<head>
    <title>AllTrips Backend</title>
</head>
<body>
    <!-- Rest of your HTML and PHP code for AllTrips_BackEnd.php -->
</body>
</html>
