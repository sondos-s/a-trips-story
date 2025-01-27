<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

function sendEmail($name, $email, $verificationCode)
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
    $mail->addAddress($email, $name); // Add recipient email and name

    $email_template = "
<div style='font-family: Arial, sans-serif;'>
    <div style='padding: 20px; text-align: center; border-bottom: 1px solid #ccc;'>
        <h2>Hello $name,</h2>
        <h3>Thank you for signing up! Please click the button below to verify your email address.</h3>
        <a href='http://localhost/a-trips-story/verify.php?token=$verificationCode' style='padding: 10px 20px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 4px;'>Verify Email</a>
    </div>
    
    <div dir='rtl' style='padding: 20px; text-align: center; border-bottom: 1px solid #ccc; background-color: #f9f9f9;'>
        <h2>مرحبًا $name ،</h2>
        <h3>شكراً لتسجيلك! يرجى النقر على الزر أدناه للتحقق من عنوان بريدك الإلكتروني.</h3>
        <a href='http://localhost/a-trips-story/verify.php?token=$verificationCode' style='padding: 10px 20px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 4px;'>تحقق من البريد الإلكتروني</a>
    </div>
    
    <div dir='rtl' style='padding: 20px; text-align: center; background-color: #e9ecef;'>
        <h2>שלום $name ,</h2>
        <h3>תודה שנרשמת! אנא לחץ על הכפתור למטה כדי לאמת את כתובת הדואר האלקטרוני שלך.</h3>
        <a href='http://localhost/a-trips-story/verify.php?token=$verificationCode' style='padding: 10px 20px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 4px;'>אמת דואר אלקטרוני</a>
    </div>
</div>";


    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Email Verification'; // Email subject
    $mail->Body = $email_template;
    // Send the email
    $mail->send();
    echo '<div id="loading-container">
            <div id="loading-spinner" class="spinner"></div>
            <br><br><br>
            <div id="loading-text">Loading</div>
        </div>';
}

if (empty($_POST["first_name"])) {
    $_SESSION["error_message"] = "First Name is required.";
    header("Location: SignUp_FrontEnd.php");
    exit();
}

if (empty($_POST["last_name"])) {
    $_SESSION["error_message"] = "Last Name is required.";
    header("Location: SignUp_FrontEnd.php");
    exit();
}

if (strlen($_POST["password"]) < 8) {
    $_SESSION["error_message"] = "Password must be at least 8 characters";
    header("Location: SignUp_FrontEnd.php");
    exit();
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $_SESSION["error_message"] = "Valid email is required.";
    header("Location: SignUp_FrontEnd.php");
    exit();
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    $_SESSION["error_message"] = "Password must contain at least one letter";
    header("Location: SignUp_FrontEnd.php");
    exit();
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    $_SESSION["error_message"] = "Password must contain at least one number";
    header("Location: SignUp_FrontEnd.php");
    exit();
}

if ($_POST["password"] !== $_POST["confirm_password"]) {
    $_SESSION["error_message"] = "Passwords must match.";
    header("Location: SignUp_FrontEnd.php");
    exit();
}

$mysqli = require __DIR__ . "/UDB.php"; // Establish a database connection

$userEmail = $_POST["email"];

$selectSql = "SELECT emailAddress, verified FROM users WHERE emailAddress = ?";
$stmt = $mysqli->prepare($selectSql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $isVerified = $row["verified"];

    if ($isVerified == true) {
        $_SESSION["error_message"] = "An account with this email address already exists. <br>Please log in or use the 'Forgot Password' option if you need to recover your account.";
        header("Location: SignUp_FrontEnd.php");
        exit();
    } else {
        $_SESSION["error_message"] = "An account with this email address already exists, but it has not been verified yet. <br>Please check your email for a verification link.";
        header("Location: SignUp_FrontEnd.php");
        exit();
    }
}

$verificationCode = uniqid(); // Generate a unique verification code

$sql = "INSERT INTO users (firstName, lastName, username, passwordUser, phoneNumber, city, birthDate, emailAddress, enableNotification, verification_code, AddedDate)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";


$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL Error: " . $mysqli->error);
}

$phoneNumber = isset($_POST["phoneNumber"]) ? $_POST["phoneNumber"] : "";
$cityName = isset($_POST["city"]) ? $_POST["city"] : "";

// Query the cities table to get the ID corresponding to the selected city name
$cityIdQuery = "SELECT id FROM cities WHERE cityName = ?";
$cityIdStmt = $mysqli->prepare($cityIdQuery);
$cityIdStmt->bind_param("s", $cityName);
$cityIdStmt->execute();
$cityIdResult = $cityIdStmt->get_result();
$cityIdRow = $cityIdResult->fetch_assoc();
$cityId = $cityIdRow["id"];

$enableNotification = isset($_POST["enableNotification"]) ? 1 : null;

// Hashing the password
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

$name = $_POST["first_name"];
$email = $_POST["email"];

$stmt->bind_param(
    "ssssssssss",
    $_POST["first_name"],
    $_POST["last_name"],
    $_POST["username"],
    $password, // Store the hashed password
    $phoneNumber,
    $cityId,
    $_POST["birthdate"],
    $_POST["email"],
    $enableNotification,
    $verificationCode
);

if ($stmt->execute()) {
    /*
    $deleteUnverifiedUsersSql = "DELETE FROM users WHERE verified = 0 AND TIMESTAMPDIFF(MINUTE, AddedDate, NOW()) > 2";
    if (!$mysqli->query($deleteUnverifiedUsersSql)) {
        error_log("Error deleting unverified users: " . $mysqli->error); // Log the error but do not disturb the signup flow
    }*/
    sendEmail("$name", "$email", "$verificationCode");



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
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="popup-container" id="popup">
        <div class="popup-content" style="width: 500px;">
            <a href="SignUp_FrontEnd.php" class="popup-close">×</a>
            <div><i class="fa fa-smile-o fa-3x"></i></div>
            <h2 class="popup-title" style="color: #65b468;">Success!</h2>
            <br><hr><br>
            <p class="popup-text">Thank you for signing up.</p>
            <p class="popup-text">To log in, please check your email for a verification link.</p>
            <br>
            <button id="popupokbtn" onclick="redirectToSplash()"> OK </button>
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

        function redirectToSplash() {
            // Redirect the user to Splash.php
            window.location.href = "Splash.php";
        }
    </script>
</body>

</html>
HTML;
} else {
    die($mysqli->error . " " . $mysqli->errno);
}

?>
<?php include 'SignUp_FrontEnd.php' ?>
