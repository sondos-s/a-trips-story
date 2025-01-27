<?php
    include 'UDB.php';
       
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

    
function sendEmail($firstName, $lastName,$email,$mailcontent){
    $name = $firstName . ' ' . $lastName;
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
    $mail->addAddress($email, $name); // Add recipient email and name

    $email_template=$mailcontent;

    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Trip Cancellation Notification'; // Email subject
    $mail->Body = $email_template;
    // Send the email
    $mail->send();
    echo 'Email sent successfully.';
}

    if (isset($_GET['trip_id'])) {
        $tripId = $_GET['trip_id'];
        
        // Retrieve the trip's date and time from the 'trips' table
        $selectQueryTrips = "SELECT tripDate, tripTime FROM trips WHERE tripId = ?";
        $stmtSelectTrips = $mysqli->prepare($selectQueryTrips);
        $stmtSelectTrips->bind_param("i", $tripId);
        $stmtSelectTrips->execute();
        $stmtSelectTrips->bind_result($tripDate, $tripTime);
        $stmtSelectTrips->fetch();
        $stmtSelectTrips->close();

        // Check if the trip's date and time are in the future
        $tripDateTime = strtotime("$tripDate $tripTime");
        $currentDateTime = time();

        if ($tripDateTime > $currentDateTime) {


             // Fetch users details first before deleting.
          $selectUsers = "SELECT u.firstName, u.lastName, u.emailAddress FROM users u
          JOIN bookings b ON u.id = b.userId
          WHERE b.tripId = ?";
          $stmtUsers = $mysqli->prepare($selectUsers);
          $stmtUsers->bind_param("i", $tripId);
          $stmtUsers->execute();
          $stmtUsers->bind_result($firstName, $lastName, $emailAddress);

         $users = [];
         while ($stmtUsers->fetch()) {
         $users[] = ['firstName' => $firstName, 'lastName' => $lastName, 'emailAddress' => $emailAddress];
          }
        $stmtUsers->close();

        // Retrieve the trip's date, time, and title from the 'trips' table
        $selectQueryTrips = "SELECT tripDate, tripTime, tripTitle FROM trips WHERE tripId = ?";
        $stmtSelectTrips = $mysqli->prepare($selectQueryTrips);
        $stmtSelectTrips->bind_param("i", $tripId);
        $stmtSelectTrips->execute();
        $stmtSelectTrips->bind_result($tripDate, $tripTime, $tripTitle);
        $stmtSelectTrips->fetch(); 
        $stmtSelectTrips->close();

            // Delete related data from 'waitinglists' table
            $deleteQueryWaitingLists = "DELETE FROM waitinglists WHERE tripId = ?";
            $stmtWaitingLists = $mysqli->prepare($deleteQueryWaitingLists);
            $stmtWaitingLists->bind_param("i", $tripId);
            $stmtWaitingLists->execute();
            $stmtWaitingLists->close();

            // Delete related data from 'wishlists' table
            $deleteQueryWishLists = "DELETE FROM wishlists WHERE tripId = ?";
            $stmtWishLists = $mysqli->prepare($deleteQueryWishLists);
            $stmtWishLists->bind_param("i", $tripId);
            $stmtWishLists->execute();
            $stmtWishLists->close();

            // Delete related data from 'bookings' table
            $deleteQueryBookings = "DELETE FROM bookings WHERE tripId = ?";
            $stmtBookings = $mysqli->prepare($deleteQueryBookings);
            $stmtBookings->bind_param("i", $tripId);
            $stmtBookings->execute();
            $stmtBookings->close();
        
            // Delete from 'trips' table
            $deleteQueryTrips = "DELETE FROM trips WHERE tripId = ?";
            $stmtTrips = $mysqli->prepare($deleteQueryTrips);
            $stmtTrips->bind_param("i", $tripId);
            $stmtTrips->execute();
            $stmtTrips->close();

            // Check if the deletion was successful
            $stmtTrips = $mysqli->prepare($deleteQueryTrips);
            $stmtTrips->bind_param("i", $tripId);

            $mailContent = "
            <div style='font-family: Arial, sans-serif;'>
                <h2 style='color: #E74C3C;'>Trip Cancellation Notification</h2>
                
                <div>
                    <p>Dear Traveler,</p>
                    <p>We deeply regret to inform you that the trip titled <strong>'$tripTitle'</strong> that you had booked, has been canceled by the owner. We understand the inconvenience this may cause and sincerely apologize for the unforeseen circumstances that led to this decision.</p>
                    <p>For further details, compensation information, or any inquiries, please do not hesitate to <a href='ContactUs_FrontEnd.php'>contact us</a>.</p>
                    <p>We value your understanding and patience in this matter.</p>
                    <p>Best Regards,<br>Your Travel Team</p>
                </div>
                
                <hr> <!-- Horizontal Line -->
                
                <div dir='rtl'>
                    <p>عزيزنا العميل،</p>
                    <p>نأسف بشدة لإبلاغكم بأن الرحلة بعنوان <strong>'$tripTitle'</strong> التي قمتم بحجزها، تم إلغاؤها من قبل المالك. نتفهم الإزعاج الذي قد يسببه هذا ونعتذر بصدق عن الظروف غير المتوقعة التي أدت إلى هذا القرار.</p>
                    <p>لمزيد من التفاصيل، معلومات التعويض أو أي استفسارات، لا تترددوا في <a href='ContactUs_FrontEnd.php'>التواصل معنا</a>.</p>
                    <p>نقدر تفهمكم وصبركم في هذا الأمر.</p>
                    <p>مع خالص التحية،<br>فريق السفر الخاص بكم</p>
                </div>
                
                <hr> <!-- Horizontal Line -->
                
                <div dir='rtl'>
                    <p>מטייל יקר,</p>
                    <p>אנו מתנצלים עמוקות על צורך להודיע לך כי הטיול בשם <strong>'$tripTitle'</strong> שהזמנת, בוטל על ידי הבעלים. אנו מבינים את הבעיה שזה עשוי לגרום ומתנצלים באמת על הנסיבות הבלתי צפויות שהובילו להחלטה זו.</p>
                    <p>לפרטים נוספים, מידע על פיצוי או כל שאלה, אנא אל תהסס ל<a href='ContactUs_FrontEnd.php'>יצור קשר איתנו</a>.</p>
                    <p>אנו מעריכים את הבנתך וסבלנותך בנוגע לעניין זה.</p>
                    <p>בברכה,<br>צוות הנסיעות שלך</p>
                </div>
            </div>";
                        foreach ($users as $user) {
                sendEmail($user['firstName'], $user['lastName'], $user['emailAddress'], $mailContent);
            }
            
            if ($stmtTrips->execute()) {
                // Deletion was successful, set a flag for showing the popup
                $showPopup = true;
            }
            $stmtTrips->close();

            if (isset($showPopup) && $showPopup) {
                // Popup HTML code
                echo <<<HTML
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
    <title>Trip Deleted Successfully</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="popup-container" id="popup">
        <div class="popup-content">
            <a href="TripsManagement_FrontEnd.php" class="popup-close">×</a>
            <div><i class="fa fa-check-circle-o fa-3x" id="checkicon"></i></div>
            <h2 class="popup-title" style="color: #65b468;">Trip Deleted Successfully</h2>
            <br><hr><br>
            <p class="popup-text">The selected trip has been deleted successfully.</p>
        </div>
    </div>
    <script>
        window.onload = function () {
            openPopup();
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
HTML;
            } else {
                $error = "Error deleting trip: " . $stmtTrips->error;
            }
        }
    }
?>
