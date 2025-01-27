<?php
session_start();
require __DIR__ . "/UDB.php";

if (isset($_POST['trip_id']) && isset($_SESSION['user_id'])) {
    $tripId = $_POST['trip_id'];
    $userId = $_SESSION['user_id'];
    
    // Check if the trip is already in the waitinglist
    $checkQuery = "SELECT * FROM waitinglists WHERE userId = ? AND tripId = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("ii", $userId, $tripId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows == 0) {
        // Add the trip to the waitinglist
        $insertQuery = "INSERT INTO waitinglists (userId, tripId) VALUES (?, ?)";
        $insertStmt = $mysqli->prepare($insertQuery);
        $insertStmt->bind_param("ii", $userId, $tripId);
        $insertStmt->execute();
        $insertStmt->close();
        echo "Trip added to waitinglist successfully.";
    } else {
        echo "Trip is already in your waitinglist.";
    }
} else {
    echo "Error: Trip ID or User ID not provided.";
}
?>
