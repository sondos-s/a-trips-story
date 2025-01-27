<?php
include 'Availability.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Trip Bookings</title>
</head>

<body>
    <?php include 'Header_Owner.php' ?>
    <?php
    include 'UDB.php';

    // Check if the 'trip_id' parameter is set in the URL
    if (isset($_GET['trip_id'])) {
        $tripId = $_GET['trip_id'];

        // Fetch the trip title from the trips table
        $titleQuery = "SELECT tripTitle FROM trips WHERE tripId = $tripId";
        $titleResult = $mysqli->query($titleQuery);

        if ($titleResult && $titleResult->num_rows > 0) {
            $tripTitle = $titleResult->fetch_assoc()["tripTitle"];
        } else {
            // Handle the case where the trip title is not found
            $tripTitle = "Trip Title Not Found";
        }

        // Fetch data from the bookings and users tables for the specific trip
        $query = "SELECT u.id AS userId, u.firstName, u.lastName, u.phoneNumber, c.cityName AS city, 
                         SUM(b.participants) AS totalParticipants, SUM(b.totalPrice) AS totalPrice, b.paymentMethod 
                  FROM bookings b 
                  INNER JOIN users u ON b.userId = u.id 
                  INNER JOIN cities c ON u.city = c.id
                  WHERE b.tripId = $tripId
                  GROUP BY userId, u.firstName, u.lastName, u.phoneNumber, c.cityName, b.paymentMethod
                  ORDER BY c.cityName ASC, u.firstName ASC, u.lastName ASC";
        $result = $mysqli->query($query);

    } else {
        // Handle the case where 'trip_id' is not set
        echo '<p>Invalid URL. Please select a valid trip.</p>';
        exit; // Exit the script
    }
    ?>

    <?php include 'Feature_ExportPDF.php' ?>
    <div id="pdf-content">

        <div class="container">
            <div class="table-wrapper">
                <div class="table-container">
                    <h3 style="font-size: 18px;"><?php echo $tripTitle; ?> - Trip Bookings<br><br></h3>

                    <div id="availability-progress">
                        <div class="progress-bar" style="width: <?php echo ($totalParticipants / $maxParticipants) * 100; ?>%; background-color: #8a6076;">
                            <div class="tooltip">Booked: <?php echo round(($totalParticipants)); ?></div>
                            <div class="tooltip2">Available: <?php echo round(($maxParticipants - $totalParticipants)); ?></div>
                        </div>
                    </div>
                    
                    <table>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Phone Number</th>
                            <th>City</th>
                            <th>Participants Number</th>
                            <th>Total Price</th>
                            <th>Payment Method</th>
                        </tr>

                        <?php
                        // Initialize variables to keep track of total price and total participants
                        $totalPrice = 0;
                        $totalParticipants = 0;
                        // Display bookings for the specific trip
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr class="table-row">';
                            echo '<td>' . $row["firstName"] . '</td>';
                            echo '<td>' . $row["lastName"] . '</td>';
                            echo '<td>' . $row["phoneNumber"] . '</td>';
                            echo '<td>' . $row["city"] . '</td>';
                            echo '<td>' . $row["totalParticipants"] . '</td>';
                            echo '<td>' . $row["totalPrice"] . '</td>';
                            echo '<td>' . $row["paymentMethod"] . '</td>';
                            echo '</tr>';

                            // Add the current booking's total price and participants to the totals
                            $totalPrice += $row["totalPrice"];
                            $totalParticipants += $row["totalParticipants"];
                        }

                        // Check if there are no bookings
                        if ($result->num_rows === 0) {
                            echo '<tr><td colspan="7">No bookings available for this trip.</td></tr>';
                        }

                        // Display the row with the calculated totals
                        echo '<tr class="table-row">';
                        echo '<td colspan="4"><strong>Totals:</strong></td>';
                        echo '<td><strong>' . $totalParticipants . '</strong></td>';
                        echo '<td><strong>' . $totalPrice . '</strong></td>';
                        echo '<td style="background-color: transparent;"></td>';
                        echo '</tr>';
                        ?>

                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
