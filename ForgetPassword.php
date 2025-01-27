<?php
include 'ForgotPassword_Backend.php';
session_start();
?>

<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="ViewStyles/ViewStyles.css">
<link rel="stylesheet" href="ViewStyles/SignViewStyles.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
<link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
<link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

<body>
    <?php include 'Header.php'; ?>

    <div class="container-in">
        <?php if ($reset_email_sent) : ?>
            <p class="info">A reset link has been sent to your email. Please check your inbox.</p>
        <?php else : ?>
            <form class="form-in-fp" method="POST" action="">
                <h3 style="font-size: 20px;">Reset Password</h3>
                <br>
                <?php
                    if (isset($_SESSION["error_message_mail"])) {
                        echo "<div class='error-message'>" . $_SESSION["error_message_mail"] . "</div>";
                        unset($_SESSION["error_message_mail"]); // Unset the error message after displaying it
                    } ?>
                    <label for="email" style="padding-left: 90px; font-size: 15px;">Email Address:</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="email" id="email" name="email" placeholder="Enter your Email Address" required>
                <br><br><br>
                <input class="resetpbutton" type="submit" value="Send Reset Link">
            </form>
        <?php endif; ?>
    </div>
</body>

</html>