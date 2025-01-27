<?php
session_start();
require __DIR__ . "/UDB.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';



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


// Function to validate and sanitize user input
function sanitizeInput($input)
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

if (
    isset($_POST['trip_id']) &&
    isset($_SESSION['user_id']) &&
    isset($_POST['participants']) &&
    isset($_POST['total_price']) &&
    isset($_POST['payment_method'])
) {
    $tripId = intval($_POST['trip_id']);
    $userId = $_SESSION['user_id'];
    $participants = intval($_POST['participants']);
    $totalPrice = floatval($_POST['total_price']);
    $paymentMethod = sanitizeInput($_POST['payment_method']);

    // Validate and sanitize the payment method
    if (!in_array($paymentMethod, ['cash', 'bit'])) {
        echo "Error: Invalid payment method.";
        exit;
    }

    // Establish a database connection (assuming $mysqli is configured correctly)

    // Check if the trip has available slots
    $tripQuery = "SELECT maxParticipants FROM trips WHERE tripId = ?";
    $tripStmt = $mysqli->prepare($tripQuery);
    $tripStmt->bind_param("i", $tripId);
    $tripStmt->execute();
    $tripStmt->store_result();

    if ($tripStmt->num_rows === 1) {
        $tripStmt->bind_result($maxParticipants);
        $tripStmt->fetch();

        // Check if the total participants after booking will exceed the maxParticipants
        $bookedParticipantsQuery = "SELECT SUM(participants) FROM bookings WHERE tripId = ?";
        $bookedParticipantsStmt = $mysqli->prepare($bookedParticipantsQuery);
        $bookedParticipantsStmt->bind_param("i", $tripId);
        $bookedParticipantsStmt->execute();
        $bookedParticipantsStmt->store_result();
        $bookedParticipantsStmt->bind_result($bookedParticipants);
        $bookedParticipantsStmt->fetch();

        if ($bookedParticipants + $participants <= $maxParticipants) {
            // Check if the booking already exists for the user and trip
            $existingBookingQuery = "SELECT bookingId FROM bookings WHERE userId = ? AND tripId = ?";
            $existingBookingStmt = $mysqli->prepare($existingBookingQuery);
            $existingBookingStmt->bind_param("ii", $userId, $tripId);
            $existingBookingStmt->execute();
            $existingBookingStmt->store_result();

            if ($existingBookingStmt->num_rows === 1) {
                // Booking already exists, update the existing booking
                $existingBookingStmt->bind_result($existingBookingId);
                $existingBookingStmt->fetch();

                $updateQuery = "UPDATE bookings SET participants = ?, totalPrice = ?, paymentMethod = ? WHERE bookingId = ?";
                $updateStmt = $mysqli->prepare($updateQuery);
                $updateStmt->bind_param("dssi", $participants, $totalPrice, $paymentMethod, $existingBookingId);

                if ($updateStmt->execute()) {
                    echo "Booking updated successfully.";
                } else {
                    echo "Error updating booking.";
                }

                $updateStmt->close();
            } else {
                // Booking doesn't exist, create a new booking entry
                $insertQuery = "INSERT INTO bookings (userId, tripId, participants, totalPrice, paymentMethod) VALUES (?, ?, ?, ?, ?)";
                $insertStmt = $mysqli->prepare($insertQuery);
                $insertStmt->bind_param("iiiss", $userId, $tripId, $participants, $totalPrice, $paymentMethod);

                if ($insertStmt->execute()) {
                    echo "Booking updated successfully.";
                    // Check if the user is in the waiting list for this trip
                    $waitingListQuery = "SELECT 1 FROM waitinglists WHERE userId = ? AND tripId = ?";
                    $waitingListStmt = $mysqli->prepare($waitingListQuery);
                    $waitingListStmt->bind_param("ii", $userId, $tripId);
                    $waitingListStmt->execute();
                    $waitingListStmt->store_result();

                    // If user is in the waiting list, remove them
                    if ($waitingListStmt->num_rows === 1) {
                        $removeQuery = "DELETE FROM waitinglists WHERE userId = ? AND tripId = ?";
                        $removeStmt = $mysqli->prepare($removeQuery);
                        $removeStmt->bind_param("ii", $userId, $tripId);

                        if ($removeStmt->execute()) {
                            echo "you removed from the waiting list.";
                        } else {
                            echo "Error removing user from the waiting list.";
                        }

                        $removeStmt->close();
                    }

                    $waitingListStmt->close();
                } else {
                    echo "Error creating booking.";
                }

                $insertStmt->close();

                // Check the sum of participants in all bookings for this trip
                $sumQuery = "SELECT SUM(participants) AS totalParticipants FROM bookings WHERE tripId = ?";
                $sumStmt = $mysqli->prepare($sumQuery);
                $sumStmt->bind_param("i", $tripId);
                $sumStmt->execute();
                $sumStmt->bind_result($totalParticipants);
                $sumStmt->fetch();
                $sumStmt->close();

                $totalParticipants = $totalParticipants ? $totalParticipants : 0;
                // Compare the totalParticipants with maxParticipants of this trip
                if ($totalParticipants >= $maxParticipants) {
                    // Retrieve users from the waiting list for this trip
                    $waitingListQuery = "
    SELECT u.emailAddress, u.username, t.tripTitle
    FROM waitinglists w
    JOIN users u ON w.userId = u.id
    JOIN trips t ON w.tripId = t.tripId
    WHERE w.tripId = ?";

                    $waitingListStmt = $mysqli->prepare($waitingListQuery);
                    $waitingListStmt->bind_param("i", $tripId);
                    $waitingListStmt->execute();

                    $waitingListStmt->bind_result($email, $username, $tripTitle);

                    while ($waitingListStmt->fetch()) {
                        // Prepare email content
                        $subject = "No available slots for $tripTitle";
                        $body = "
<div style='font-family: Arial, sans-serif;'>
    <div>
        Dear $username ,<br>
        We regret to inform you that there are no available slots left for the trip titled <strong>'$tripTitle'</strong>. We will notify you if a slot becomes available.<br><br>
    </div>
    
    <hr> <!-- Horizontal Line -->
    
    <div dir='rtl'>
        عزيزي/عزيزتي $username ،<br>
        نأسف لإبلاغك أنه لم يعد هناك مقاعد متوفرة للرحلة بعنوان <strong>'$tripTitle'</strong>. سنبلغك في حال توفر مكان.<br><br>
    </div>
    
    <hr> <!-- Horizontal Line -->
    
    <div dir='rtl'>
        יקר/ה $username ,<br>
        אנו מצטערים להודיע לך שאין מקומות פנויים עבור הטיול בשם <strong>'$tripTitle'</strong>. ניידע אותך אם יתפנה מקום.<br><br>
    </div>
</div>";



                        // Send email
                        sendEmail($email, $username, $subject, $body);
                    }

                    $waitingListStmt->close();
                }
            }
        } else {
            echo "Error: Maximum participants limit for this trip exceeded.";
        }
    } else {
        echo "Error: Trip not found.";
    }

    $tripStmt->close();
    $bookedParticipantsStmt->close();
} else {
    echo "Error: Required fields not provided.";
}
?>