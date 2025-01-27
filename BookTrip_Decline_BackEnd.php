<?php
include 'UDB.php';

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
    if ($mail->send()) {
        return true; // Email sent successfully
    } else {
        return false; // Email sending failed
    }
}

if (isset($_GET['booking_id'])) {
    $bookingId = intval($_GET['booking_id']);

    // Check if the booking exists
    $checkQuery = "SELECT b.userId, u.emailAddress, t.tripTitle, t.tripDate ,u.username,t.tripId
                   FROM bookings b
                   INNER JOIN users u ON b.userId = u.id
                   INNER JOIN trips t ON b.tripId = t.tripId
                   WHERE b.bookingId = ?";
    $checkStmt = $mysqli->prepare($checkQuery);

    if (!$checkStmt) {
        die("Error in checkQuery: " . mysqli_error($mysqli));
    }

    $checkStmt->bind_param("i", $bookingId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $userId = $row['userId'];
        $userEmail = $row['emailAddress'];
        $tripTitle = $row['tripTitle'];
        $tripDate = $row['tripDate'];
        $username = $row['username'];
        $tripId=$row['tripId'];

        // The booking exists, delete it
        $deleteQuery = "DELETE FROM bookings WHERE bookingId = ?";
        $deleteStmt = $mysqli->prepare($deleteQuery);

        if (!$deleteStmt) {
            die("Error in deleteQuery: " . mysqli_error($mysqli));
        }

        $deleteStmt->bind_param("i", $bookingId);

        if ($deleteStmt->execute()) {
            // Booking canceled successfully, send an email notification
            $subject = "Your Booking has been Canceled";
            $message = "
<div style='font-family: Arial, sans-serif;'>
    <h2 style='color: #2C3E50;'>Booking Declination Notification</h2>
    <h3 style='color: #E74C3C;'>$tripTitle - $tripDate</h3>
    
    <div>
        <p>Dear User,</p>
        <p>We regret to inform you that your booking request for <b>'$tripTitle'</b> on <b>$tripDate</b> has been declined due to one or more of the following reasons:</p>
        <ul>
            <li>You have previously not shown up for the trip without any prior notification.</li>
            <li>You chose to pay via bit and your payment has not been received.</li>
            <li>The trip has been overbooked.</li>
        </ul>
        <p>We apologize for the inconvenience. For further information or clarification, please do not hesitate to contact us.</p>
        <p>Sincerely,<br>Your Travel Team</p>
    </div>
    
    <hr> <!-- Horizontal Line -->
    
    <div dir='rtl'>
        <p>عزيزي العميل،</p>
        <p>نأسف لإبلاغك بأنه تم رفض طلب الحجز الخاص بك لـ <b>'$tripTitle'</b> في <b>$tripDate</b> لواحدة أو أكثر من الأسباب التالية:</p>
        <ul>
            <li>لقد قمت في الماضي بعدم الحضور للرحلة دون إشعار مسبق.</li>
            <li>اخترت الدفع عبر التطبيق ولم يتم استلام الدفع.</li>
            <li>تم حجز الرحلة بالكامل.</li>
        </ul>
        <p>نعتذر عن الإزعاج، لمزيد من المعلومات أو لأي استفسارات، يرجى التواصل معنا.</p>
        <p>مع الاحترام،<br>فريق السفر الخاص بك</p>
    </div>
    
    <hr> <!-- Horizontal Line -->
    
    <div dir='rtl'>
        <p>משתמש יקר,</p>
        <p>אנו מצטערים להודיע לך שבקשת ההזמנה שלך עבור <b>'$tripTitle'</b> בתאריך <b>$tripDate</b> נדחתה מהסיבות הבאות אחת או יותר:</p>
        <ul>
            <li>בעבר לא הגעת לטיול ללא התראה מוקדמת.</li>
            <li>בחרת לשלם דרך ביט והתשלום לא התקבל.</li>
            <li>הטיול מוכרז כמלא.</li>
        </ul>
        <p>אנו מתנצלים על אי הנוחות. למידע נוסף או לבירורים, אנא צרו קשר איתנו.</p>
        <p>בברכה,<br>צוות הנסיעות שלך</p>
    </div>
</div>";


            // Check the waiting list for people with the same tripId as the deleted booking.
            $waitingListQuery = "SELECT users.emailAddress,users.username FROM waitinglists INNER JOIN users ON waitinglists.userId = users.id WHERE waitinglists.tripId = ?";
            $waitingListStmt = $mysqli->prepare($waitingListQuery);
            if (!$waitingListStmt) {
                die("Error in waitingListQuery: " . mysqli_error($mysqli));
            }
            $waitingListStmt->bind_param("i", $tripId);
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

            if (sendEmail($userEmail, $username, $subject, $message)) {
                // Email sent successfully, redirect to a confirmation page
                header("Location: TripsBookingsManagement_FrontEnd.php");
                exit();
            } else {
                // Email sending failed
                echo "Error: Unable to send email notification.";
            }
        } else {
            // Error occurred while canceling booking
            echo "Error: Unable to cancel the booking.";
        }



        $waitingListStmt->close();
        $deleteStmt->close();
    } else {
        // Booking not found, display an error message
        echo "Error: Booking not found.";
    }

    $checkStmt->close();
} else {
    // Booking ID not provided, redirect to an error page or display an error message
    echo "Error: Booking ID not provided.";
}
