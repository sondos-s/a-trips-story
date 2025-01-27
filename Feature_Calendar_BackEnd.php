<?php
include 'UDB.php';

$currentMonth = $_GET["month"];
$currentYear = $_GET["year"];

$trips = array();

$sql = "SELECT tripId, tripTitle, DAY(tripDate) AS tripDay FROM trips WHERE MONTH(tripDate) = ? AND YEAR(tripDate) = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $currentMonth, $currentYear);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $trips[$row["tripDay"]] = $row["tripTitle"];
        $trips[] = array(
            "tripId" => $row["tripId"],
            "title" => $row["tripTitle"]
        );
    }
}

$stmt->close();

echo json_encode($trips);
?>
