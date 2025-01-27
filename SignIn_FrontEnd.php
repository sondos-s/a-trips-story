<?php include 'SignIn_BackEnd.php' ?>
<!DOCTYPE html>
<html>

<head>
    <title>Sign In</title>
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
</head>

<body>
    <?php include 'Header.php'; ?>
    <div class="container-in">
    
        <form class="form-in" method="POST" action="">
            <h3>Sign In</h3>
            <?php
    if (isset($_SESSION['error_message'])) {
        echo '<p style="color:red;">' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
    ?>
            <br>
            <div class="form-group-in">
                <label  for="identifier">Username:</label>
                <input type="text" name="identifier" id="identifier" placeholder="Enter your Username/Email" required>
            </div>
            <div class="form-group-in">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your Password" required>
            </div>
            <br>
            <input class="signinbutton" type="submit" value="Login">

            <div class="mt-3" style="font-size: 14px;align-items: left;">
                <br><br>
                <a href="ForgetPassword.php" style="color: blue; margin-right: 20px; font-weight: 100px;">Forgot password?</a>
                <p style="margin-bottom: 0; font-weight: 100px;">Don't have an account yet?<a href="SignUp_FrontEnd.php"
                        style="color: blue;">Sign Up</a></p>
            </div>
        </form>
    </div>
</body>

</html>