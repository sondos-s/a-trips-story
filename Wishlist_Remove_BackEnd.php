<?php
session_start();
require __DIR__ . "/UDB.php";

if (isset($_POST['trip_id']) && isset($_SESSION['user_id'])) {
    $tripId = $_POST['trip_id'];
    $userId = $_SESSION['user_id'];

    // Check if the trip is in the user's wishlist
    $checkQuery = "SELECT * FROM wishlists WHERE userId = ? AND tripId = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("ii", $userId, $tripId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Remove the trip from the wishlist
        $removeQuery = "DELETE FROM wishlists WHERE userId = ? AND tripId = ?";
        $removeStmt = $mysqli->prepare($removeQuery);
        $removeStmt->bind_param("ii", $userId, $tripId);
        $removeStmt->execute();
        $removeStmt->close();
        echo "Trip removed from wishlist successfully.";
    } else {
        echo "Trip is not in your wishlist.";
    }
} else {
    echo "Error: Trip ID or User ID not provided.";
}
?>
