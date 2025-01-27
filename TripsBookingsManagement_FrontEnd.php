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
    <body>
    <?php include 'Header_Owner.php' ?>
    <?php
    include 'UDB.php';

    // Fetch data from the bookings table, joining trips and users tables
    $query = "SELECT 
        t.tripTitle, t.tripDate, 
        u.firstName, u.lastName, u.username, u.phoneNumber, u.city, u.birthDate, u.emailAddress, 
        b.participants, b.totalPrice, b.paymentMethod, b.bookingId
        FROM bookings b 
        INNER JOIN trips t ON b.tripId = t.tripId
        INNER JOIN users u ON b.userId = u.id
        ORDER BY t.tripDate ASC";

    $result = $mysqli->query($query);
    ?>

    <?php include 'Feature_ExportPDF.php' ?>
    <div id="pdf-content">

        <div class="container-atb">
            <div class="table-wrapper-atb">
                <div class="table-container-atb">
                    <h3 style="font-size: 18px;">Trips' Bookings<br><br></h3>

                    <table>
                    <tr>
                        <th>Trip Title</th>
                        <th>Trip Date</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>Phone Number</th>
                        <th>City</th>
                        <th>Birth Date</th>
                        <th>Email Address</th>
                        <th>Participants</th>
                        <th>Total Price</th>
                        <th>Payment Method</th>
                        <th>Confirm/Decline<th>
                    </tr>

                    <?php
                    // Display booking details for all trips
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr class="table-row">';
                        echo '<td>' . $row["tripTitle"] . '</td>';
                        echo '<td>' . $row["tripDate"] . '</td>';
                        echo '<td>' . $row["firstName"] . '</td>';
                        echo '<td>' . $row["lastName"] . '</td>';
                        echo '<td>' . $row["username"] . '</td>';
                        echo '<td>' . $row["phoneNumber"] . '</td>';
                        echo '<td>' . $row["city"] . '</td>';
                        echo '<td>' . $row["birthDate"] . '</td>';
                        echo '<td>' . $row["emailAddress"] . '</td>';
                        echo '<td>' . $row["participants"] . '</td>';
                        echo '<td>' . $row["totalPrice"] . '</td>';
                        echo '<td>' . $row["paymentMethod"] . '</td>';
                        echo '<td>';
                        echo '<a href="ConfirmBooking.php?booking_id=' . $row['bookingId'] . '" class="confirm-link" data-booking-id="' . $row['bookingId'] . '" onclick="confirmConfirmationBooking(event)"><i class="fa fa-check-circle-o"></i> Confirm</a>';
                        echo '<a href="BookTrip_Decline_BackEnd.php?booking_id=' . $row['bookingId'] . '" class="cancel-link" onclick="confirmDeclineBooking()"><i class="fa fa-times-circle-o"></i> Decline</a>';
                        echo '</td>';
                        echo '</tr>';
                    }

                    // Check if there are no bookings
                    if ($result->num_rows === 0) {
                        echo '<tr><td colspan="13">No bookings available.</td></tr>';
                    }
                    ?>

                </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    function confirmDeclineBooking() {
        if (confirm("Are you sure you want to decline this booking?")) {
            // User clicked OK, you can proceed with the decline
        }  else {
        // User clicked Cancel, prevent the default behavior (closing the dialog)
        event.preventDefault();
        }
    }
</script>

<script>
   function confirmConfirmationBooking(event) {
    if (confirm("Are you sure you want to confirm this booking?")) {
        // User clicked OK, proceed with the confirmation

        // Retrieve the booking ID from the clicked link
        const bookingId = event.target.getAttribute("data-booking-id");

        // Hide the corresponding table row on the client side
        const tableRow = event.target.closest("tr");
        if (tableRow) {
            tableRow.style.display = "none"; // Hide the row

            // Store the information about the hidden row in local storage
            // You can use the bookingId as a key to uniquely identify the hidden row
            localStorage.setItem(`hiddenRow-${bookingId}`, "true");
        }

        // You can now perform further actions, such as sending a confirmation request to the server
        // using AJAX or any other method. If you don't want to delete the data from the database,
        // you can update the booking status or perform other relevant actions on the server.

    } else {
        // User clicked Cancel, prevent the default behavior (closing the dialog)
        event.preventDefault();
    }
}

// When the page loads, check local storage and hide rows that should be hidden
window.addEventListener("load", function () {
    const tableRows = document.querySelectorAll(".table-row");
    tableRows.forEach((row) => {
        const bookingId = row.querySelector(".confirm-link").getAttribute("data-booking-id");
        const isHidden = localStorage.getItem(`hiddenRow-${bookingId}`);
        if (isHidden === "true") {
            row.style.display = "none"; // Hide the row
        }
    });
});

</script>