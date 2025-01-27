<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/model.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Trip Added Successfully</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    include 'UDB.php';


    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require 'vendor/autoload.php';


    function sendEmail($firstName, $lastName, $email, $mailcontent)
    {
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

        $email_template = $mailcontent;

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'UPDATES'; // Email subject
        $mail->Body = $email_template;

        // Send the email
        if ($mail->send()) {
            echo 'Email sent successfully to ' . $email . '.';
        } else {
            echo 'Error sending email to ' . $email . ': ' . $mail->ErrorInfo;
        }
    }

    // Check if in edit mode
    $editMode = isset($_GET['edit_trip']) && $_GET['edit_trip'] === 'true';

    // Initialize variables for prefilling data
    $prefilledData = [];
    $prefilledCategoryIds = [];

    if ($editMode) {
        // Fetch trip data from the database based on trip ID
        $tripId = $_GET['trip_id'];
        $query = "SELECT * FROM trips WHERE tripId = $tripId";
        $result = $mysqli->query($query);

        if ($result) {
            $prefilledData = $result->fetch_assoc(); // Fetch data for prefilling

            // Fetch categories associated with the trip
            $categoryQuery = "SELECT tripCategory FROM trips WHERE tripId = $tripId";
            $categoryResult = $mysqli->query($categoryQuery);

            if ($categoryResult) {
                $categoryRow = $categoryResult->fetch_assoc();
                $prefilledCategoryIds = explode(', ', $categoryRow['tripCategory']);
            }
        } else {
            // Handle database error
            echo "Error fetching trip data: " . $mysqli->error;
        }
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process form data and update/create trip in the database
        $tripTitle = $_POST['tripTitle'];
        $tripLocation = $_POST['tripLocation'];
        $tripDate = $_POST['tripDate'];
        $tripTime = $_POST['tripTime'];
        $tripPrice = $_POST['tripPrice'];
        $maxParticipants = $_POST['maxParticipants'];
        $tripItineraries = $_POST['tripItineraries'];
        $selectedCategories = isset($_POST['tripCategory']) ? $_POST['tripCategory'] : [];

        $categoryString = implode(', ', $selectedCategories);

        if ($editMode) {
            // Perform database update based on trip ID
            $updateQuery = "UPDATE trips SET tripTitle = '$tripTitle', tripLocation = '$tripLocation', 
                            tripDate = '$tripDate', tripTime = '$tripTime', tripPrice = '$tripPrice', 
                            maxParticipants = '$maxParticipants', tripItineraries = '$tripItineraries', 
                            tripCategory = '$categoryString' WHERE tripId = $tripId";
            $updateResult = $mysqli->query($updateQuery);

            if ($updateResult) {
                // Fetch all users' email, firstName, and lastName who have booked this trip
                $selectUsers = "SELECT u.firstName, u.lastName, u.emailAddress FROM users u
                                JOIN bookings b ON u.id = b.userId
                                WHERE b.tripId = $tripId";
                $resultUsers = $mysqli->query($selectUsers);

                if ($resultUsers) {
                    $mailContent = "
                    <div style='font-family: Arial, sans-serif;'>
                        <h2 style='color: #2C3E50;'>Trip Update Notification</h2>
                        <h3 style='color: #E74C3C;'>$tripTitle</h3>
                        
                        <div>
                            <p>Dear $username,</p>
                            <p>We would like to inform you that there have been some updates to the trip <b>'$tripTitle'</b> that you have booked. We encourage you to review the updates and let us know if there are any concerns.</p>
                            <p>Click <a href='link_to_your_trip_page'>here</a> to view the updates.</p>
                            <p>We apologize for any inconvenience and thank you for your understanding and cooperation.</p>
                            <p>Sincerely,<br>Your Travel Team</p>
                        </div>
                        <hr>
                        <div dir='rtl'>
                            <p>عزيزي ،</p>
                            <p>نود إبلاغكم بأنه قد تم إجراء بعض التحديثات على الرحلة <b>'$tripTitle'</b> التي قمت بحجزها. نشجعكم على مراجعة التحديثات وإعلامنا إذا كانت هناك أي مخاوف.</p>
                            <p>انقر <a href='link_to_your_trip_page'>هنا</a> لعرض التحديثات.</p>
                            <p>نعتذر عن أي إزعاج ونشكركم على تفهمكم وتعاونكم.</p>
                            <p>مع خالص التحية،<br>فريق السفر الخاص بك</p>
                        </div>
                        <hr>
                        <div dir='rtl'>
                            <p>יקר,</p>
                            <p>נרצה ליידע אותך כי היו כמה עדכונים לטיול <b>'$tripTitle'</b> שהזמנת. אנו ממליצים לך לבדוק את העדכונים וליידע אותנו אם ישנם חששות.</p>
                            <p>לחץ <a href='link_to_your_trip_page'>כאן</a> כדי לראות את העדכונים.</p>
                            <p>אנו מתנצלים על אי הנוחות ומודים לך על ההבנה והשיתוף פעולה שלך.</p>
                            <p>בברכה,<br>צוות הנסיעות שלך</p>
                        </div>
                    </div>";
                    
                    while ($user = $resultUsers->fetch_assoc()) {
                        sendEmail($user['firstName'], $user['lastName'], $user['emailAddress'], $mailContent);
                    }
                }


                if ($updateResult) {
                    // Redirect to trips management page after successful update
                    header("Location: TripsManagement_FrontEnd.php");
                    exit();
                } else {
                    // Handle update error
                    echo "Error updating trip: " . $mysqli->error;
                }
            } else {
                // Perform database insert for new trip
                $insertQuery = "INSERT INTO trips (tripTitle, tripLocation, tripDate, tripTime, tripPrice, 
                            maxParticipants, tripItineraries, tripCategory) VALUES 
                            ('$tripTitle', '$tripLocation', '$tripDate', '$tripTime', '$tripPrice', 
                            '$maxParticipants', '$tripItineraries', '$categoryString')";
                $insertResult = $mysqli->query($insertQuery);

                if ($insertResult) {

                    // Redirect to a certain page or just exit
                    exit();
                } else {
                    // Handle insert error
                    echo "Error creating new trip: " . $mysqli->error;
                }
            }
        }
    }
    ?>
    <div class="popup-container" id="popup">
        <div class="popup-content">
            <a href="UpdateTrip_FrontEnd.php" class="popup-close">×</a>
            <div><i class="fa fa-smile-o fa-3x"></i></div>
            <h2 class="popup-title" style="color: #65b468;">Success!</h2>
            <hr><br>
            <p class="popup-text">Your trip has been successfully saved.</p>
            <br>
            <button id="popupokbtn" onclick="redirectToTripsManagement()" style="width: 180px;"> OK </button>
        </div>
    </div>
    </div>

    <script>
        window.onload = function() {
            openPopup();
        };

        function openPopup() {
            document.getElementById("popup").classList.add("active");
        }

        function closePopup() {
            document.getElementById("popup").classList.remove("active");
        }

        function redirectToTripsManagement() {
            // Redirect the user to TripsManagement_FrontEnd.php
            window.location.href = "TripsManagement_FrontEnd.php";
        }
    </script>
</body>

</html>
<?php include 'UpdateTrip_FrontEnd.php' ?>