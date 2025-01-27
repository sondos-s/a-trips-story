<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Trips Management</title>
</head>

<body>
    <?php include 'Header_Owner.php' ?>
    <?php
    include 'UDB.php';

    // Fetch trip data from the trips table
    $query = "SELECT * FROM trips ORDER BY tripDate DESC";
    $result = $mysqli->query($query);
    ?>

    <div class="container">
        <div class="add-button" onmouseover="playSound()">
            <a href="UpdateTrip_FrontEnd.php" class="add-button-link"><i class="fa fa-plus fa-lg"></i></a>
        </div>
        <div class="table-wrapper">
            <div class="table-container">
                <h3 style="font-size: 18px; padding-top: 50px;">Trips Managament</h3>
                <br><br>
                <table>
                    <tr>
                        <th>Trip Title</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Price</th>
                        <th>Max Participants</th>
                        <th>Itineraries</th>
                        <th>Categories</th>
                        <th>Delete</th>
                        <th>Edit</th>
                    </tr>

                    <?php
            $todayTrips = array(); // Array to store trips scheduled for today
            $otherTrips = array(); // Array to store trips scheduled for other days

            while ($row = $result->fetch_assoc()) {
                // Get the trip date and convert it to a timestamp
                $tripDate = strtotime($row["tripDate"]);
                $todayDate = strtotime(date("Y-m-d")); // Get today's date as a timestamp

                // Check if the trip date is today
                $isToday = ($tripDate === $todayDate);

                // Add the row data to the appropriate array
                if ($isToday) {
                    $todayTrips[] = $row;
                } else {
                    $otherTrips[] = $row;
                }
            }

            // Display today's trips first
            foreach ($todayTrips as $row) {
            echo '<tr class="table-row highlight-row" style="background-color: #f9f9e5;">';

            // Add the pin icon for today's trip
            echo '<td>';
            echo '📌';
            echo $row["tripTitle"];
            echo '</td>';

            // Fetch location name based on location ID
            $locationId = $row["tripLocation"];
            $locationQuery = "SELECT locationName FROM locations WHERE id = $locationId";
            $locationResult = $mysqli->query($locationQuery);
            $locationRow = $locationResult->fetch_assoc();
            $locationName = $locationRow["locationName"];
            echo '<td>' . $locationName . '</td>'; // Display location name instead of ID

            echo '<td>' . $row["tripDate"] . '</td>';
            echo '<td>' . $row["tripTime"] . '</td>';
            echo '<td>' . $row["tripPrice"] . '</td>';
            echo '<td>' . $row["maxParticipants"] . '</td>';
            echo '<td>' . nl2br($row["tripItineraries"]) . '</td>';
            
            echo '<td>';
            if (!empty($row["tripCategory"])) {
                $categoryIds = explode(', ', $row["tripCategory"]);
                $categoryNames = [];

                // Retrieve category names based on category IDs
                foreach ($categoryIds as $categoryId) {
                    $categoryQuery = "SELECT categoryName FROM tripcategories WHERE categoryId = $categoryId";
                    $categoryResult = $mysqli->query($categoryQuery);
                    $categoryRow = $categoryResult->fetch_assoc();
                    $categoryNames[] = $categoryRow["categoryName"];
                }

                echo implode(', ', $categoryNames);
            } else {
                echo 'No categories';
            }
            echo '</td>';

            echo '<td>';
                echo '<a href="TripsManagement_Delete_BackEnd.php?trip_id=' . $row["tripId"] . '" class="action-link trash-icon">';
                    echo '<i class="fa fa-trash fa-lg"></i>';
                echo '</a>';
            echo '</td>';

            echo '<td>';
                echo '<a href="UpdateTrip_FrontEnd.php?edit_trip=true&trip_id=' . $row["tripId"] . '&trip_data=' . base64_encode(json_encode($row)) . '" class="action-link edit-icon">';
                    echo '<i class="fa fa-pencil-square-o fa-lg"></i>';
                echo '</a>';
            echo '</td>';
                
            echo '</tr>';
        }
            // Display other trips
            foreach ($otherTrips as $row) {
                        echo '<tr class="table-row">';
                        echo '<td>' . $row["tripTitle"] . '</td>';

                        // Fetch location name based on location ID
                        $locationId = $row["tripLocation"];
                        $locationQuery = "SELECT locationName FROM locations WHERE id = $locationId";
                        $locationResult = $mysqli->query($locationQuery);
                        $locationRow = $locationResult->fetch_assoc();
                        $locationName = $locationRow["locationName"];
                        echo '<td>' . $locationName . '</td>'; // Display location name instead of ID

                        echo '<td>' . $row["tripDate"] . '</td>';
                        echo '<td>' . $row["tripTime"] . '</td>';
                        echo '<td>' . $row["tripPrice"] . '</td>';
                        echo '<td>' . $row["maxParticipants"] . '</td>';
                        echo '<td>' . nl2br($row["tripItineraries"]) . '</td>';
                        
                        echo '<td>';
                        if (!empty($row["tripCategory"])) {
                            $categoryIds = explode(', ', $row["tripCategory"]);
                            $categoryNames = [];

                            // Retrieve category names based on category IDs
                            foreach ($categoryIds as $categoryId) {
                                $categoryQuery = "SELECT categoryName FROM tripcategories WHERE categoryId = $categoryId";
                                $categoryResult = $mysqli->query($categoryQuery);
                                $categoryRow = $categoryResult->fetch_assoc();
                                $categoryNames[] = $categoryRow["categoryName"];
                            }

                            echo implode(', ', $categoryNames);
                        } else {
                            echo 'No categories';
                        }
                        echo '</td>';

                        echo '<td>';
                            echo '<a href="javascript:void(0);" onclick="confirmDelete(' . $row["tripId"] . ', \'' . $row["tripDate"] . '\');" class="action-link trash-icon">';
                                echo '<i class="fa fa-trash fa-lg"></i>';
                            echo '</a>';
                        echo '</td>';
                        
                        echo '<td>';
                            echo '<a href="UpdateTrip_FrontEnd.php?edit_trip=true&trip_id=' . $row["tripId"] . '&trip_data=' . base64_encode(json_encode($row)) . '" class="action-link edit-icon">';
                                echo '<i class="fa fa-pencil-square-o fa-lg"></i>';
                            echo '</a>';
                        echo '</td>';
                            
                    echo '</tr>';
                }
    ?>
                </table>
            </div>
        </div>
    </div>
    <audio id="soundPop">
        <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
    </audio>
    <script>
        window.onload = function () {
            <?php
            if (isset($_SESSION['trip_deleted']) && $_SESSION['trip_deleted']) {
                echo "openPopup();";
                unset($_SESSION['trip_deleted']); // Clear the session variable
            }
            ?>
        };

        function openPopup() {
            document.getElementById("popup").classList.add("active");
        }

        function closePopup() {
            document.getElementById("popup").classList.remove("active");
        }
    </script>
</body>

</html>

<script>
    // Function to play the sound
    function playSound() {
        var sound = document.getElementById("soundPop");
        if (sound) {
            sound.currentTime = 0; // Reset the sound to the beginning
            sound.play();
        } else {
            console.log("Sound element not found.");
        }
    }
  </script>

<script>
    function confirmDelete(tripId, tripDate) {
        var tripDateTime = new Date(tripDate).getTime();
        var currentDateTime = new Date().getTime();

        if (tripDateTime <= currentDateTime) {
            alert("You cannot delete a past trip.");
        } else {
            var result = confirm("Are you sure you want to delete this trip?");
            if (result) {
                // User clicked "OK," proceed with the delete action
                window.location.href = 'TripsManagement_Delete_BackEnd.php?trip_id=' + tripId;
            } else {
                // User clicked "Cancel," do nothing
            }
        }
    }
</script>


