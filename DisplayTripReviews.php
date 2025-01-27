<?php

// Include the existing database connection from UDB.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['trip_id'])) {
    $tripId = $_GET['trip_id'];
  
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        $userId=0;
    }
    $mysqli = require __DIR__ . "/UDB.php";

    // Calculate the average rating for the trip
    $sql = "SELECT AVG(rate) AS averageRating FROM reviews WHERE tripId = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $tripId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $averageRating = $row['averageRating'];

    // Split the average rating into integer and fractional parts
    // Check if $averageRating is not null before using floor
    if ($averageRating !== null) {
        $integerPart = floor($averageRating);
        $fractionalPart = $averageRating - $integerPart;
    } else {
        // Handle the case when $averageRating is null (e.g., set default values)
        $integerPart = 0;
        $fractionalPart = 0;
    }

?>
    <div class="left-side">
        <div class="average-rating">
            <p>Trip Rating:
                <?php $averageRating = $averageRating ?? 0;?>
                <?php echo number_format($averageRating, 2); ?>
            </p>
        </div>
    </div>
    <!-- // End of left-side -->
    <?php

    // Close the first statement and its result set
    $stmt->close();
    $result->close();



    $sql = "SELECT u.username, r.review, r.rate, r.picture_paths,u.id,r.reviewId
    FROM reviews AS r
    LEFT JOIN users AS u ON r.userId = u.id
    WHERE r.tripId = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $tripId);
    $stmt->execute();
    $result = $stmt->get_result();

    ?>
    <div class="right-side">
        <div class="user-reviews">
            <?php
             if ($result->num_rows === 0) {
                echo '<p>There are no reviews yet.</p>';
            } else{
            while ($row = $result->fetch_assoc()) {
                $rating = $row['rate'];
                $userId = $row['id'];
                $reviewId = $row['reviewId'];

            ?>
                <!-- Start of the review box -->
                <div class="user-review-box">
                    <div class="user-review">


                        <p><strong>
                                <?php echo $row['username'] ?>
                            </strong></p>

                        <div class="stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                            ?>
                                    <span class="fa fa-star checked"></span>
                                <?php

                                } else {
                                ?>
                                    <span class="fa fa-star-o"></span>
                            <?php
                                }
                            }
                            ?>

                        </div>
                        <p>
                            <?php echo $row['review'] ?>
                        </p>

                        <?php
                        // Display photos, if available
                        if (!empty($row['picture_paths'])) {

                            $photoUrls = json_decode($row['picture_paths']);

                        ?>
                            <div class="review-photos">
                                <?php
                                if (!empty($photoUrls))

                                    foreach ($photoUrls as $photoUrl) {

                                ?>
                                    <img src="<?php echo htmlspecialchars($photoUrl) ?>" class="review-img" alt="Review Photo">
                                <?php
                                    }
                                ?>
                            </div>
                        <?php
                        }

                        // Check if the user is the author of this review
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) {
                            echo '<br>';
                            echo '<form action="deleteReview.php" method="POST" onsubmit="return confirmDelete();">';
                            echo '<center>';
                            echo '<input type="hidden" name="reviewId" value="' . $row['reviewId'] . '">';
                            echo '<input type="submit" value="Delete" class="delete-button">';
                            echo '</center>';
                            echo '</form>';
                        }

                        ?>
                    </div>

                </div>
                <!-- // End of the review box -->
            <?php
            }
        }

            ?>

        </div>
    </div>

<?php

    $stmt->close();
    $mysqli->close();
} ?>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this review?");
    }
</script>