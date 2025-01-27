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

    // Separate trips into past and planned based on their dates
    $currentDate = date('Y-m-d');
    $pastTrips = [];
    $plannedTrips = [];

    
    foreach ($curlHandles as $tripId => $ch) {
        $response = curl_multi_getcontent($ch);
        $data = json_decode($response, true);
    
        // Check if data is valid
        if ($data !== null && isset($data['title'])) {
            $imageUrl = isset($data['thumbnail']['source']) ? $data['thumbnail']['source'] : '';
            $tripTitle = $data['title'];
    
            // Fetch date and price from the database for the current trip
            $mysqli = require __DIR__ . "/UDB.php";
            $tripDetailsQuery = "SELECT tripDate, tripPrice FROM trips WHERE tripId = $tripId";
            $tripDetailsResult = $mysqli->query($tripDetailsQuery);
            $tripDetails = $tripDetailsResult->fetch_assoc();
    
            // Check if tripDetails is valid
            if ($tripDetails !== null) {
                $date = $tripDetails['tripDate'];
                $price = $tripDetails['tripPrice'];
    
                if ($date < $currentDate) {
                    $pastTrips[] = [
                        'title' => $tripTitle,
                        'date' => $date,
                        'price' => $price,
                        'image' => $imageUrl,
                        'trip_id' => $tripId,
                    ];
                } else {
                    $plannedTrips[] = [
                        'title' => $tripTitle,
                        'date' => $date,
                        'price' => $price,
                        'image' => $imageUrl,
                        'trip_id' => $tripId,
                    ];
                }
            } else {
                // Handle the case where tripDetails is null
                // You may want to log an error or take some other action
            }
        } else {
            // Handle the case where data is null or doesn't have the expected structure
            // You may want to log an error or take some other action
        }
    
        // Close the cURL handle for this trip
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    
    // Close the multi-cURL handle
    curl_multi_close($mh);
?>

