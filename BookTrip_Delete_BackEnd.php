<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';
session_start();
require __DIR__ . "/UDB.php";


function sendEmail($email, $username, $subject, $body)
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Your SMTP host
    $mail->SMTPAuth = true;
    $mail->Username = 'atripsstory@gmail.com'; // Your SMTP username
    $mail->Password = 'efalcuznugkbvhgx'; // Your SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption
    $mail->Port = 587; // TCP port to connect to

    // Get the recipient's name (optional)

    // Recipients
    $mail->setFrom('atripsstory@gmail.com', 'Qest Meshwar - A Trip\'s Story');
    $mail->addAddress($email, $username); // Add recipient email and name


    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $body;
    // Send the email
    $mail->send();
}


if (isset($_GET['booking_id'])) {
    $bookingId = intval($_GET['booking_id']);
    $userId = $_SESSION['user_id'];

    $checkQuery = "SELECT * FROM bookings WHERE bookingId = ? AND userId = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    if (!$checkStmt) {
        die("Error in checkQuery: " . mysqli_error($mysqli));
    }

    $checkStmt->bind_param("ii", $bookingId, $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 1) {
        // Fetch the necessary details before deletion
        $userInfoQuery = "SELECT users.firstName, users.lastName, trips.tripTitle, trips.tripId,bookings.participants
                          FROM bookings 
                          INNER JOIN users ON bookings.userId = users.id 
                          INNER JOIN trips ON bookings.tripId = trips.tripId 
                          WHERE bookings.bookingId = ?";
        $userInfoStmt = $mysqli->prepare($userInfoQuery);
        if (!$userInfoStmt) {
            die("Error in userInfoQuery: " . mysqli_error($mysqli));
        }

        $userInfoStmt->bind_param("i", $bookingId);
        $userInfoStmt->execute();
        $userInfoStmt->bind_result($firstName, $lastName, $tripTitle, $tripId, $participants);

        // Store the details in an associative array or variables before deletion
        if ($userInfoStmt->fetch()) {
            $bookingDetails = [
                'username' => "$firstName $lastName",
                'tripTitle' => $tripTitle,
                'tripId' => $tripId,
                'participants' => $participants
            ];
        } else {
            echo 'Error: User or Trip not found.';
            exit();
        }

        $userInfoStmt->close();

        // Now perform the deletion
        $deleteQuery = "DELETE FROM bookings WHERE bookingId = ?";
        $deleteStmt = $mysqli->prepare($deleteQuery);
        if (!$deleteStmt) {
            die("Error in deleteQuery: " . mysqli_error($mysqli));
        }

        $deleteStmt->bind_param("i", $bookingId);
        if ($deleteStmt->execute()) {
            // Send the email after successful deletion
            sendEmail(
                'atripsstory@gmail.com',
                'Balkes Wishahi',
                'Booking Cancellation Notification',
                "Dear Balkes Wishahi, <br/> ". $bookingDetails['username']." has canceled the booking  for the trip titled '$tripTitle'."
            );

            // Check the waiting list for people with the same tripId as the deleted booking.
            $waitingListQuery = "SELECT users.emailAddress,users.username FROM waitinglists INNER JOIN users ON waitinglists.userId = users.id WHERE waitinglists.tripId = ?";
            $waitingListStmt = $mysqli->prepare($waitingListQuery);
            if (!$waitingListStmt) {
                die("Error in waitingListQuery: " . mysqli_error($mysqli));
            }

            $waitingListStmt->bind_param("i", $bookingDetails['tripId']);
            $waitingListStmt->execute();
            $waitingListResult = $waitingListStmt->get_result();

            // Send emails to users in the waiting list
            while ($row = $waitingListResult->fetch_assoc()) {
                $email = $row['emailAddress'];
                $username = $row['username'];
                // Customize the email content as per your requirements.

                $mailContent = "
Dear $username,<br/><br/>
We are excited to inform you that a slot has become available for the trip titled '<strong>$tripTitle</strong>'.<br/>
Number of available slots: <strong>$participants</strong>.<br/><br/>
If you are still interested in joining this trip, please respond as soon as possible to secure your spot. Spaces are limited and will be allocated on a first-come, first-served basis.<br/><br/>
Warm Regards<br/>
<hr/>

عزيزي <br/><br/>
نسعد بإبلاغك أن هناك مقعدًا متاحًا الآن في الرحلة بعنوان '<strong>$tripTitle</strong>'.<br/>
عدد المقاعد المتاحة: <strong>$participants</strong>.<br/><br/>
إذا كنت ما زلت مهتمًا في الانضمام إلى هذه الرحلة، يرجى الرد في أقرب وقت ممكن لتأمين مقعدك. المقاعد محدودة وسيتم تخصيصها وفقًا لأسبقية الوصول.<br/><br/>
تحية طيبة<br/>
<hr/>

יקר ,<br/><br/>
אנו שמחים להודיע לך כי פתחה עצמה עמדה בטיול בכותרת '<strong>$tripTitle</strong>'.<br/>
מספר המקומות הפנויים: <strong>$participants</strong>.<br/><br/>
אם אתה עדיין מעוניין להצטרף לטיול זה, נא להגיב בהקדם כדי להבטיח את מקומך. המקומות מוגבלים וינתנו על בסיס מי שמגיע ראשון מקבל ראשון.<br/><br/>
בברכה<br/>

";


                sendEmail($email, $username, "Spot Available: '$tripTitle'!", $mailContent);
            }

            $waitingListStmt->close();

            $showPopup = true;
        } else {
            echo "Error deleting booking: " . mysqli_error($mysqli);
        }

        $deleteStmt->close();
    } else {
        echo "User is not authorized to cancel this booking or booking does not exist.";
    }

    $mysqli->close();
} else {
    echo "Error: Booking ID not provided.";
}
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
    <title>Booking Canceled Successfully</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    if (isset($showPopup) && $showPopup) {
        // Popup HTML code
        echo <<<HTML
    <div class="overlay" id="overlay"></div>
    <div class="popup-container" id="popup">
        <div class="popup-content">
            <a href="javascript:void(0);" onclick="closePopup();" class="popup-close">×</a>
            <div><i class="fa fa-check-circle-o fa-3x" id="checkicon"></i></div>
            <h2 class="popup-title" style="color: #65b468;">Booking Canceled Successfully</h2>
            <br><hr><br>
            <p class="popup-text">The booking has been canceled successfully.</p>
        </div>
    </div>
    <script>
        window.onload = function () {
            openPopup();
        };

        function openPopup() {
            document.getElementById("popup").classList.add("active");
            document.getElementById("overlay").style.display = "block"; // Show the overlay
        }

        function closePopup() {
            document.getElementById("popup").classList.remove("active");
            window.history.back(); // Navigate back to the previous page
        }
    </script>
HTML;
    }
    //test
    ?>
</body>

</html>