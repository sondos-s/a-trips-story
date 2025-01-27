<?php
// Include necessary configurations and database connections
include 'ProfileEdit_BackEnd.php';

// Fetch the user's booked trips
$userId = $userDetails["id"]; // Assuming $userDetails contains user information
$query = "SELECT b.*, t.tripTitle, t.tripDate, t.tripTime FROM bookings AS b
        INNER JOIN trips AS t ON b.tripId = t.tripId
        WHERE b.userId = $userId
        ORDER BY t.tripDate DESC";

$result = $mysqli->query($query);
?>