<?php
$mysqli = require __DIR__ . "/UDB.php";
$tripId = $_GET['trip_id'];

$query = "SELECT CONCAT(users.firstName, ' ', users.lastName) AS fullname, reviews.review, reviews.rate, reviews.picture_paths
          FROM reviews
          JOIN users ON reviews.userId = users.id
          WHERE reviews.tripId = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $tripId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews</title>
    <style>
        .star {
            font-size: 24px;
            color: gray;
        }

        .star.rated {
            color: gold;
        }
    </style>
</head>
<body>

<div class='reviews'>
    <?php
    while ($row = $result->fetch_assoc()) {
        $pictures = explode(' ', $row['picture_paths']);
        echo "<div class='review'>";
        echo "<h2>" . htmlspecialchars($row['fullname']) . "</h2>";

        // Display stars for rating
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $row['rate']) {
                echo "<span class='star rated'>&#9733;</span>"; // Filled star
            } else {
                echo "<span class='star'>&#9734;</span>"; // Empty star
            }
        }

        echo "<p>Review: " . htmlspecialchars($row['review']) . "</p>";

        // Display each picture
        foreach ($pictures as $picture) {
            echo "<img src='" . htmlspecialchars($picture) . "' alt='Review Picture'>";
        }

        echo "</div>";  // End of review div
    }
    ?>
</div>

</body>
</html>

<?php
$stmt->close();
$mysqli->close();
?>
