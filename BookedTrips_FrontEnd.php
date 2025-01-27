<?php include 'BookedTrips_BackEnd.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Booked Trips</title>
</head>

<body>
    <?php include "Header.php"; ?>
    <div id="hiddenfortop">
        <div id="fullBookedTrips">
            <div class="title-bar">
                <h1 style="font-size: 24px;">Booked Trips</h1>
            </div>
            <div class="container">
                <div class="table-wrapper">
                    <div class="table-container">
                        <table>
                            <tr>
                                <th>Trip Title</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Participants</th>
                                <th>Total Price</th>
                                <th>Payment Method</th>
                                <th>Update/Cancel Booking</th>
                            </tr>
                            <?php
                            // Get the current date in the same format as $row['tripDate']
                            $currentDate = date("Y-m-d");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Get the trip title and id
                                    $tripTitle = $row['tripTitle'];
                                    $tripId = $row['tripId'];

                                    // Make an API call to Wikipedia to fetch the trip image
                                    $tripTitleEncoded = urlencode(str_replace(' ', '_', $tripTitle));
                                    $apiUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/{$tripTitleEncoded}";
                                    $apiResponse = file_get_contents($apiUrl);
                                    $apiData = json_decode($apiResponse, true);
                                    $imageUrl = isset($apiData['thumbnail']['source']) ? $apiData['thumbnail']['source'] : '';

                                    // Check if the trip date is in the past
                                    $isPastDate = $row['tripDate'] < $currentDate;
                                    /// cancel more than two days before trip date
                                    // Calculate the difference in days between the trip date and current date
                                    $tripDate = strtotime($row['tripDate']);
                                    $daysUntilTrip = floor(($tripDate - strtotime(date('Y-m-d'))) / (60 * 60 * 24));

                                    // Check if the trip date is in two days or less
                                    $isTwoDaysOrLess = $daysUntilTrip <= 2;

                                    // Output each booked trip as a table row
                                    echo '<tr class="table-row">';
                                    echo '<td><a href="TripDetails_FrontEnd2.php?trip_id=' . $tripId . '&tripTitle=' . urlencode($tripTitle) . '" style="text-decoration: underline; color: black; font-weight: bold;">';
                                    echo '<div class="square-image-container"><img src="' . $imageUrl . '" alt="Trip Image" style="width: 80px; height: 80px; overflow: hidden;"></div>';
                                    echo '<div>' . $tripTitle . '</div></a></td>';
                                    echo '<td>' . $row['tripDate'] . '</td>'; // Display the trip date
                                    echo '<td>' . $row['tripTime'] . '</td>'; // Display the trip time
                                    echo '<td>' . $row['participants'] . '</td>'; // Display the number of participants
                                    echo '<td>' . $row['totalPrice'] . '</td>'; // Display the total price
                                    echo '<td>' . $row['paymentMethod'] . '</td>'; // Display the payment method

                                    // Check if the trip date is in the past and apply styling accordingly
                                    if ($isPastDate || $isTwoDaysOrLess) {
                                        echo '<td>';
                                        echo '<a style="color: grey;" class="update-link" disabled><i class="fa fa-pencil-square-o"></i> Update</a>';
                                        echo '<a style="color: grey;" class="cancel-link" disabled><i class="fa fa-times"></i> Cancel</a>';
                                        echo '</td>';
                                    } else {
                                        // Display the "Update" and "Cancel" links
                                        echo '<td>';
                                        echo '<a href="BookTrip_FrontEnd.php?booking_id=' . $row['bookingId'] . '&trip_id=' . $row['tripId'] . '" class="update-link"><i class="fa fa-pencil-square-o"></i> Update</a>';
                                        echo '<a href="BookTrip_Delete_BackEnd.php?booking_id=' . $row['bookingId'] . '" class="cancel-link" onclick="confirmCancelBooking()"><i class="fa fa-times"></i> Cancel</a>';
                                        echo '</td>';
                                    }

                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7">No booked trips found.</td></tr>';
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    function confirmCancelBooking() {
        if (confirm("Are you sure you want to cancel this booking?")) {
            // User clicked OK, you can proceed with the cancellation
        } else {
            // User clicked Cancel, prevent the default behavior (closing the dialog)
            event.preventDefault();
        }
    }
</script>