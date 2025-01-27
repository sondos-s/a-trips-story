<?php
session_start();
if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__ . "/UDB.php";
    $sql = "SELECT * FROM `users` WHERE id={$_SESSION["user_id"]}";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();
}

if (isset($_GET['category'])) {
    $categoryName = $_GET['category'];

    // Include the existing database connection from UDB.php
    $mysqli = require __DIR__ . "/UDB.php";

    // Check if the connection was successful
    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // Prepare the query with proper escaping to prevent SQL injection
    $escapedCategoryName = $mysqli->real_escape_string($categoryName);

    // Retrieve the 'categoryId' for the given category name
    $categoryQuery = "SELECT categoryId FROM tripcategories WHERE categoryName = '$escapedCategoryName'";
    $categoryResult = $mysqli->query($categoryQuery);

    if (!$categoryResult) {
        die("Database query error: " . $mysqli->error);
    }

    // Fetch the 'categoryId' from the result
    $categoryRow = $categoryResult->fetch_assoc();
    $categoryId = $categoryRow['categoryId'];

    // Use the 'categoryId' to filter trips from the 'trips' table
    $query = "SELECT t.* FROM trips t
              INNER JOIN tripcategories c ON t.tripCategory = c.categoryId
              WHERE c.categoryName = '$escapedCategoryName'";

    // Execute the query and check for errors
    $result = $mysqli->query($query);

    if (!$result) {
        die("Database query error: " . $mysqli->error);
    }

    // Initialize an array to store trips
    $trips = [];

    while ($row = $result->fetch_assoc()) {
        $tripId = $row['tripId'];
        $tripTitle = $row['tripTitle'];
        $tripDate = $row['tripDate'];
        $tripPrice = $row['tripPrice'];
        
         // Fetch image from Wikipedia API response
         $tripTitleEncoded = urlencode(str_replace(' ', '_', $tripTitle));
         $apiUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/{$tripTitleEncoded}";
         $apiResponse = file_get_contents($apiUrl);
         $apiData = json_decode($apiResponse, true);
         $imageUrl = isset($apiData['thumbnail']['source']) ? $apiData['thumbnail']['source'] : '';

        // Check if the trip date is in the future
        $today = date("Y-m-d");
        if ($tripDate >= $today) {
            $trips[] = [
                'trip_id' => $tripId,
                'title' => $tripTitle,
                'date' => $tripDate,
                'price' => $tripPrice,
                'image' => $imageUrl,
            ];
        }
    }

    // Close the database connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/HomeViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
</head>

<body>
    <?php include 'Header.php'; ?>
    <div id="hiddenfortop">
        <div class="page-container">
            <div class="">
                <?php if (isset($categoryName)) : ?>
                    <?php
                    $categoryEmoji = '';
                    switch ($categoryName) {
                        case 'Hiking':
                            $categoryEmoji = '🏔️';
                            break;
                        case 'Heritage':
                            $categoryEmoji = '🏛️';
                            break;
                        case 'Camping':
                            $categoryEmoji = '🏕️';
                            break;
                        case 'Cultural':
                            $categoryEmoji = '📜';
                            break;
                        case 'Cruise':
                            $categoryEmoji = '🚢';
                            break;
                        case 'Safari':
                            $categoryEmoji = '🦁';
                            break;
                    }
                    ?>
                    <h2><?php echo $categoryEmoji; ?> Explore <?php echo htmlspecialchars($categoryName); ?> Trips</h2>
                <?php else : ?>
                    <h2>Category not specified</h2>
                <?php endif; ?>
            </div>
                <div class="trips-list-c">
                    <?php if (isset($categoryName) && count($trips) > 0) : ?>
                        <h3 style="padding-top: 100px; display: flex;">Available <?php echo htmlspecialchars($categoryName); ?> Trips</h3>
                        <div class="content-container">
                            <?php foreach ($trips as $trip) : ?>
                                <div class="content-item"
                                style="background-color: white; margin-right: 20px; padding: 20px 20px; border-radius: 10px; align-items: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
                                <img src="<?php echo $trip['image']; ?>" alt="Trip Image"
                                    style="width: 150px; height: 150px;">
                                <p><a
                                        href="TripDetails_FrontEnd2.php?trip_id=<?php echo $trip['trip_id']; ?>&tripTitle=<?php echo urlencode($trip['title']); ?>">
                                        <?php echo $trip['title']; ?>
                                    </a></p>
                                <p style="font-size: 14px;">Date:
                                    <?php echo $trip['date']; ?>
                                </p>
                                <p style="font-size: 14px;">Price:
                                    <?php echo $trip['price']; ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else : ?>
                            <p><i class="fa fa-frown-o"></i>&nbsp;&nbsp;Unfortunately, there are no <?php echo htmlspecialchars($categoryName); ?> Trips available at the moment.</p>
                            <p>Please check back later for updates or explore our other exciting categories.&nbsp;&nbsp;<i class="fa fa-search"></i></p>
                        <?php endif; ?>
                </div>
        </div>
    </div>
</body>

</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tripsContainer = document.querySelector(".content-container");
        const categoryName = "<?php echo isset($categoryName) ? htmlspecialchars($categoryName) : ''; ?>";

        if (categoryName && tripsContainer && tripsContainer.children.length === 0) {
            const message = document.createElement("p");
            message.innerHTML = `Unfortunately, there are no ${categoryName} Trips available at the moment. Please check back later for updates.`;
            tripsContainer.appendChild(message);
        }
    });
</script>
