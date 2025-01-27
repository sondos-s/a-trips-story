<?php
include 'ProfileEdit_BackEnd.php';

// Check if booking_id and trip_id parameters are provided in the URL
if (isset($_GET['booking_id']) && isset($_GET['trip_id'])) {
    $bookingId = $_GET['booking_id'];
    $tripId = $_GET['trip_id'];

    // Query the database to fetch the specific booking details
    $bookingQuery = "SELECT * FROM bookings WHERE bookingId = $bookingId";
    $bookingResult = $mysqli->query($bookingQuery);

    if ($bookingResult) {
        $bookingDetails = $bookingResult->fetch_assoc();
    } else {
        echo "Error fetching booking details: " . $mysqli->error;
    }
}
?>