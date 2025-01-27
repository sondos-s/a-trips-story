<?php
 session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/model.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <title>
    </title>
</head>

<body>
    <div>
        <?php

        // Include the existing database connection from UDB.php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);



        if (isset($_GET['trip_id'])) {
            $tripId = $_GET['trip_id'];
            $userId = $_SESSION['user_id'];
            //$userId = 25;
            $mysqli = require __DIR__ . "/UDB.php";


            $sql = "SELECT * FROM trips WHERE tripId = $tripId";
            $result = $mysqli->query($sql);

            if ($result->num_rows > 0) {
                $tripData = $result->fetch_assoc();
                $tripDate = $tripData['tripDate'];
            }

            // Check if $tripData is not null
            if ($tripData !== null) {
                // Check if the trip date is valid before comparing
                if (!empty($tripData['tripDate'])) {
                    // Convert trip date to a timestamp
                    $tripDate = strtotime($tripData['tripDate']);
                    // Get the current date as a timestamp
                    $currentDate = strtotime(date("Y-m-d"));

                    if (!empty($tripData['tripDate'])) {
                        // Convert trip date to a timestamp
                        $tripDate = strtotime($tripData['tripDate']);
                        // Get the current date as a timestamp
                        $currentDate = strtotime(date("Y-m-d"));

                        // Check if the trip date is later than today
                        if ($tripDate < $currentDate) {
                            echo "<div>";
                            echo "<center><button id='addReviewButton' class='booktripbutton' onclick='openModal()'>add review</button></center><br><br>";
                            echo "</div>";
                            ?>
                            <br><br>
                            <div id="customerReviews">
                                <h4>Reviews</h4>
                                <?php include('DisplayTripReviews.php'); ?>
                            </div>

                            <?php
                        }
                    }
                }
            }
        }
        ?><!-- Modal -->
        <div id="imageModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeImageModal()">&times;</span> <!-- Add onclick attribute here -->

                <img src="" id="preview-img">
            </div>
        </div>
        <div id="myModal" class="modal">
            <center>
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span> <!-- Add onclick attribute here -->
                    <form id="reviewForm" enctype="multipart/form-data"
                        action="AddReview.php?trip_id=<?php echo $_GET['trip_id']; ?>" method="POST">
                        <label for="review">How was the trip?</label><br>
                        <textarea id="review" name="review" rows="4" cols="50" required></textarea><br><br>

                        <div class="rating">
                            <label for="rating">Rate the trip:</label><br>
                            <div class="stars stars_rev">
                                <input type="radio" name="rating" id="star1" value="5" required><label
                                    for="star1"></label>
                                <input type="radio" name="rating" id="star2" value="4"><label for="star2"></label>
                                <input type="radio" name="rating" id="star3" value="3"><label for="star3"></label>
                                <input type="radio" name="rating" id="star4" value="2"><label for="star4"></label>
                                <input type="radio" name="rating" id="star5" value="1" ><label for="star5"></label>
                            </div>
                        </div><br>

                        <label for="pictures">Upload Pictures:</label><br>
                        <input type="file" id="pictures" name="pictures[]" multiple accept="image/*"><br><br>

                        <input type="submit" value="Add review">
                    </form>
                </div>
            </center>
        </div>

        <script>

        </script>


        <script>
            // Get the modal and button elements
            var modal = document.getElementById("myModal");
            var button = document.getElementById("addReviewButton");

            // Function to open the modal
            function openModal() {
                modal.style.display = "block";
            }



            // Function to close the modal
            function closeModal() {
                var modal = document.getElementById("myModal");
                modal.style.display = "none";
            }

            // Event listener for the button click
            button.addEventListener("click", openModal);

            // Event listener to close the modal if the user clicks outside of it
            window.addEventListener("click", function (event) {
                if (event.target == modal) {
                    closeModal();
                }
            });



            // image model

            var imageModal = document.getElementById("imageModal");

            var images = document.getElementsByClassName("review-img");

            for (var i = 0; i < images.length; i++) {
                images[i].addEventListener('click', function () {
                    imageModal.style.display = "block";
                    console.log(this.getAttribute('src'));
                    
                    document.getElementById("preview-img").setAttribute('src',this.getAttribute('src'));
                }, false);
            }

            function closeImageModal() {
               
                
                imageModal.style.display = "none";
            }


        </script>
</body>

</html>