<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';
$reset_email_sent = false;


function sendEmail($name, $email, $resetToken)
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
    <div style='width: 100%; font-family: Arial, sans-serif;'>
        <div style='background-color: #f4f4f4; padding: 20px; text-align: center;'>
            <h2 style='margin-bottom: 10px;'>Hello, $name</h2>
            <p style='margin-bottom: 20px;'>We received a request to reset your password. If this was you, please click the button below to reset your password. If you didn't request this, please ignore this email.</p>
            <a href='http://localhost/a-trips-story/ResetPassword.php?token=$resetToken' style='padding: 10px 15px; background-color: #007BFF; color: #ffffff; text-decoration: none; border-radius: 4px;'>Reset Password</a>
        </div>
    <hr>
        <div style='background-color: #e6e6e6; padding: 20px; text-align: center; direction: rtl;'>
            <h2 style='margin-bottom: 10px;'>مرحبًا، $name</h2>
            <p style='margin-bottom: 20px;'>تلقينا طلبًا لإعادة تعيين كلمة مرورك. إذا كنت أنت، الرجاء النقر على الزر أدناه لإعادة تعيين كلمة المرور الخاصة بك. إذا لم تطلب هذا، يرجى تجاهل هذا البريد الإلكتروني.</p>
            <a href='http://localhost/a-trips-story/ResetPassword.php?token=$resetToken' style='padding: 10px 15px; background-color: #007BFF; color: #ffffff; text-decoration: none; border-radius: 4px;'>إعادة تعيين كلمة المرور</a>
        </div>
    <hr>
        <div style='background-color: #d4d4d4; padding: 20px; text-align: center; direction: rtl;'>
            <h2 style='margin-bottom: 10px;'>שלום, $name</h2>
            <p style='margin-bottom: 20px;'>קיבלנו בקשה לאפס את סיסמתך. אם זה אתה, אנא לחץ על הכפתור למטה כדי לאפס את סיסמתך. אם לא ביקשת זאת, אנא התעלם מהאימייל הזה.</p>
            <a href='http://localhost/a-trips-story/ResetPassword.php?token=$resetToken' style='padding: 10px 15px; background-color: #007BFF; color: #ffffff; text-decoration: none; border-radius: 4px;'>אפס סיסמה</a>
        </div>
    </div>";


    // Content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Reset password'; // Email subject
    $mail->Body = $email_template;
    // Send the email
    $mail->send();
    echo 'Email sent successfully.';
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $mysqli = require __DIR__ . "/UDB.php";
    $email = $_POST["email"];

    // Check if email exists in DB
    $stmt = $mysqli->prepare("SELECT id, username FROM users WHERE emailAddress = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $username);
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $token = bin2hex(random_bytes(16));
        $stmt->fetch();

        // Store token in the DB with a 1-hour expiration time
        $expires = new DateTime();
        $expires->modify('+1 hour');
        $stmt = $mysqli->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires->format('Y-m-d H:i:s'));
        $stmt->execute();
        $stmt->close();

        // Send email with the reset link containing the token
        sendEmail($username, $email, $token);

        $reset_email_sent = true;
        header('Location: GoToReset.php');
        exit;
    } else {
        // Instead of redirecting, show an alert on the same page.
        echo "<script>alert('The Email Address is Not Registered or Verified. Please Enter a Valid Email Address.');</script>";
    }
    $stmt->close();
}
