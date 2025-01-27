<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Handle when the user is not logged in
    echo "NotLoggedIn";
    exit;
}

$userId = $_SESSION['user_id'];

// Include the existing database connection from UDB.php
$mysqli = require __DIR__ . "/UDB.php";

// Prepare and execute the SQL query to fetch the user's waiting list
$query = "SELECT tripId FROM waitinglists WHERE userId = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$waitinglist = [];
while ($row = $result->fetch_assoc()) {
    $waitinglist[] = $row['tripId'];
}

echo json_encode($waitinglist);
?>
