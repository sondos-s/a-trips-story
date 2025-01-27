<?php
include 'UDB.php';

// Check if the 'trip_id' parameter is set in the URL
if (isset($_GET['trip_id'])) {
    $tripId = $_GET['trip_id'];

    // Fetch the trip title and maxParticipants from the trips table
    $tripQuery = "SELECT tripTitle, maxParticipants FROM trips WHERE tripId = $tripId";
    $tripResult = $mysqli->query($tripQuery);

    if ($tripResult && $tripResult->num_rows > 0) {
        $tripData = $tripResult->fetch_assoc();
        $tripTitle = $tripData["tripTitle"];
        $maxParticipants = $tripData["maxParticipants"];
    } else {
        // Handle the case where the trip data is not found
        $tripTitle = "Trip Not Found";
        $maxParticipants = 0;
    }

    // Fetch the total number of participants for the specific trip
    $participantsQuery = "SELECT SUM(participants) AS totalParticipants FROM bookings WHERE tripId = $tripId";
    $participantsResult = $mysqli->query($participantsQuery);

    if ($participantsResult && $participantsResult->num_rows > 0) {
        $participantsData = $participantsResult->fetch_assoc();
        $totalParticipants = $participantsData["totalParticipants"];
    } else {
        // Handle the case where the participants data is not found
        $totalParticipants = 0;
    }

    // Calculate the availability percentage
    $availabilityPercentage = ($totalParticipants / $maxParticipants) * 100;
} else {
    // Handle the case where 'trip_id' is not set
    echo '<p>Invalid URL. Please select a valid trip.</p>';
    exit; // Exit the script
}
?>
