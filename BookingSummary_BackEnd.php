<?php
// Include the existing database connection from UDB.php
$mysqli = require __DIR__ . "/UDB.php";
include "BookingSummary_FrontEnd.php";
?>

<?php

// Retrieve data from query parameters
$participants = $_GET['participants'];
$paymentMethod = $_GET['paymentMethod'];
$userId = $_GET['user_id']; // Add this line to retrieve the user ID

// Get the trip ID from the query parameters
if (isset($_GET["trip_id"])) {
    $tripId = $_GET["trip_id"];

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
        $locationStmt->bind_param("i", $tripData["tripLocation"]);
        $locationStmt->execute();
        $locationResult = $locationStmt->get_result();

        if ($locationResult->num_rows > 0) {
            $locationData = $locationResult->fetch_assoc();
            $locationName = $locationData["locationName"];
        } else {
            $locationName = "Unknown Location";
        }

        // Fetch user information based on user ID
        $userQuery = "SELECT users.*, locations.locationName AS cityName FROM users LEFT JOIN locations ON users.city = locations.id WHERE users.id = ?";

        $userStmt = $mysqli->prepare($userQuery);
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();

        if ($userResult->num_rows > 0) {
            $userData = $userResult->fetch_assoc();
            $userName1 = $userData["firstName"] . " " . $userData["lastName"];
            $userEmail = $userData["emailAddress"];
            $userName = $userData["username"];
            $userPhoneNumber = $userData["phoneNumber"];
            $userCity = $userData["cityName"]; // Use cityName instead of city

        } else {
            $userName = "Unknown User";
        }
        // Prepare and execute the SQL query to retrieve booking details including total price
        $bookingQuery = "SELECT `bookingId`, `userId`, `tripId`, `participants`, `totalPrice`, `paymentMethod` FROM `bookings` WHERE 1";
        $bookingResult = $mysqli->query($bookingQuery);

        if ($bookingResult->num_rows > 0) {
            $bookingData = $bookingResult->fetch_assoc();
            $totalPrice = $bookingData["totalPrice"]; // Fetch the total price from the booking data
        } else {
            $totalPrice = "Unknown"; // Set a default value if the booking data is not found
        }

        // Output the data as HTML
        echo "<html>";
        echo "<head>";
        echo "<title>Booking Summary</title>";
        echo "</head>";
        echo "<body >";
        echo '<div class="bs">';
        echo "<h3 style=\"padding-left: 70px;\">Booking Summary</h3>";
        echo "<br><br>";
        echo "<p style=\"padding-left: 100px; font-family: \'Courier New\', Courier, monospace;\"><strong>Full Name:</strong> " . $userName1 . "</p>";

        echo "<p style=\"padding-left: 100px; font-family: 'Courier New', Courier, monospace;\"><strong>Email Address:</strong> " . $userEmail . "</p>"; // Display the email address

        echo "<p style=\"padding-left: 100px; font-family: 'Courier New', Courier, monospace;\"><strong>Username:</strong> " . $userName . "</p>";

        echo "<p style=\"padding-left: 100px; font-family: 'Courier New', Courier, monospace;\"><strong>Phone Number:</strong> " . $userPhoneNumber . "</p>";

        echo "<p style=\"padding-left: 100px; font-family: 'Courier New', Courier, monospace;\"><strong>City:</strong> " . $userCity . "</p>";

        echo "<br><br>";

        echo "<hr>";

        echo "<h5 style=\"padding-left: 70px;\">Booking Information:</h5>";
        echo "<p style=\"padding-left: 100px; font-family: \'Courier New\', Courier, monospace;\"><strong>Booked Slots Number:</strong> " . $participants . "</p>";

        echo "<p style=\"padding-left: 100px; font-family: \'Courier New\', Courier, monospace;\"><strong>Total Price:</strong> ₪" . $totalPrice . "</p>"; // Display the total price
        echo "<p style=\"padding-left: 100px; font-family: \'Courier New\', Courier, monospace;\"><strong>Chosen Payment Method:</strong> " . $paymentMethod . "</p>";

        echo "<hr>";

        echo "<h5 style=\"padding-left: 70px;\">Booked Trip Details:</h5>";
        echo "<p style=\"padding-left: 100px; font-family: \'Courier New\', Courier, monospace;\"><strong>Title:</strong> " . $tripData["tripTitle"] . "</p>";
        echo "<p style=\"padding-left: 100px; font-family: \'Courier New\', Courier, monospace;\"><strong>Location:</strong> " . $locationName . "</p>";
        echo "<p style=\"padding-left: 100px; font-family: \'Courier New\', Courier, monospace;\"><strong>Date:</strong> " . $tripData["tripDate"] . "</p>";
        echo "<p style=\"padding-left: 100px; font-family: 'Courier New', Courier, monospace;\"><strong>Time:</strong> " . date("H:i", strtotime($tripData["tripTime"])) . "</p>";


        echo "<hr>";

        echo "<br>";
        echo "<p style=\"padding-left: 70px; font-weight: bold; color: #6082B6;\">Pay Attention!</p>
        <p style=\"padding-left: 70px; font-weight: bold; font-family: \'Courier New\', Courier, monospace; color: #6082B6;\">1. Your booking is not confirmed until we approve it and send a Booking Confirmation to your Email Address.</p>
        <p style=\"padding-left: 70px; font-weight: bold; font-family: \'Courier New\', Courier, monospace; color: #6082B6;\">2. The latest allowable cancellation date is 2 days before the trip. </p>";
        echo "<br>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
    } else {
        echo "<p>No trip found with the given ID.</p>";
    }
} else {
    echo "<p>Trip ID not provided.</p>";
}
?>
