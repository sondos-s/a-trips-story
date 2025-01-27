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
        $imageUrl = isset($data['thumbnail']['source']) ? $data['thumbnail']['source'] : '';
        $tripTitle = $data['title'];

        // Fetch date and price from the database for the current trip
        $mysqli = require __DIR__ . "/UDB.php";
        $tripDetailsQuery = "SELECT tripDate, tripPrice FROM trips WHERE tripId = $tripId";
        $tripDetailsResult = $mysqli->query($tripDetailsQuery);
        $tripDetails = $tripDetailsResult->fetch_assoc();
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

        // Close the database connection for this trip
        $mysqli->close();

        // Close the cURL handle for this trip
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    // Close the multi-cURL handle
    curl_multi_close($mh);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/HomeViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>All Trips</title>
</head>
<body>
    <h2>Planned Trips:</h2>
    <div class="plannedtrips" style="display: flex;">
        <?php foreach ($plannedTrips as $trip) : ?>
            <div style="align-items: center;">
                <img src="<?php echo $trip['image']; ?>" alt="Trip Image" style="width: 150px; height: 150px; margin-right: 20px;">
                <p><a href="TripDetails_FrontEnd.php?trip_id=<?php echo $trip['trip_id']; ?>&tripTitle=<?php echo urlencode($trip['title']); ?>"><?php echo $trip['title']; ?></a></p>
                <p>Date: <?php echo $trip['date']; ?></p>
                <p>Price: <?php echo $trip['price']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <br><br><br><br><br><br>
    <h2>Past Trips:</h2>
    <div class="pasttrips" style="display: flex;">
        <?php foreach ($pastTrips as $trip) : ?>
            <div style="align-items: center;">
                <img src="<?php echo $trip['image']; ?>" alt="Trip Image" style="width: 150px; height: 150px; margin-right: 20px;">
                <p><a href="TripDetails_FrontEnd.php?trip_id=<?php echo $trip['trip_id']; ?>&tripTitle=<?php echo urlencode($trip['title']); ?>"><?php echo $trip['title']; ?></a></p>
                <p>Date: <?php echo $trip['date']; ?></p>
                <p>Price: <?php echo $trip['price']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
