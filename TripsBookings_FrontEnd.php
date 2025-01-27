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
    <title>Trips' Bookings</title>
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
        <div class="table-wrapper">
            <div class="table-container">
                <h3 style="font-size: 18px; padding-top: 50px;">Trips' Bookings</h3>
                <br>
                <table>
                    <tr>
                        <th>Trip Title</th>
                        <th>Date</th>
                        <th>Price</th>
                        <th>Max Participants</th>
                        <th>Itineraries</th>
                        <th>Bookings</th>
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

            echo '<td>' . $row["tripDate"] . '</td>';
            echo '<td>' . $row["tripPrice"] . '</td>';
            echo '<td>' . $row["maxParticipants"] . '</td>';
            echo '<td>' . nl2br($row["tripItineraries"]) . '</td>';

            echo '<td>';
            echo '<a href="TripBookings_FrontEnd.php?trip_id=' . $row["tripId"] . '" class="action-link view-bookings-button">';
            echo 'View Bookings';
            echo '</a>';
            echo '</td>';

                
            echo '</tr>';
        }
            // Display other trips
            foreach ($otherTrips as $row) {
                        echo '<tr class="table-row">';
                        echo '<td>' . $row["tripTitle"] . '</td>';

                        echo '<td>' . $row["tripDate"] . '</td>';
                        echo '<td>' . $row["tripPrice"] . '</td>';
                        echo '<td>' . $row["maxParticipants"] . '</td>';
                        echo '<td>' . nl2br($row["tripItineraries"]) . '</td>';

                        echo '<td>';
                        echo '<a href="TripBookings_FrontEnd.php?trip_id=' . $row["tripId"] . '" class="action-link view-bookings-button">';
                        echo 'View Bookings';
                        echo '</a>';
                        echo '</td>';

                            
                    echo '</tr>';
                }
    ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>