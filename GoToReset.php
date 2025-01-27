<!DOCTYPE html>
<html>

<head>
    <title>Check Email to Reset Password</title>
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
    <div class="container-in-ss">
        <form class="form-in-ss" method="POST" action="">
            <br>
            <br>
            <br>
            <br>
            <center>
            <p style="font-weight: bold; font-family: \'Courier New\', Courier, monospace;">
                Thank you for requesting a password reset.
            </p>
            <br>
            <p style="font-weight: bold; font-family: \'Courier New\', Courier, monospace;">
                Please check your email for a reset password link. 
            </p>
            <p style="font-weight: bold; font-family: \'Courier New\', Courier, monospace;">
                If not recieved within a few minutes, check spam.
            </p>
            <br><br>
            <p style="font-weight: bold; font-family: \'Courier New\', Courier, monospace; color: #6082B6;">
                <i class="fa fa-shield" style="color: #7393B3;"></i>&nbsp;Link expires in one hour for security reasons.
            </p>
            <p style="font-weight: bold; font-family: \'Courier New\', Courier, monospace; color: #6082B6;"> 
                If needed, you can request another reset.
            </p>
            <br>
            <br>
            <br>
            <br>
            </center>
        </form>
    </div>
</body>

</html>