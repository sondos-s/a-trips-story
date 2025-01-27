<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it's not already started
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

</head>

<body id="infoscreen">
    <div class="header" id="header">
        <div class="logo">
            <?php
                // Check if the user is logged in or not
                if (isset($_SESSION["user_id"])) {
                    // User is logged in, logo link redirects to Home.php
                    echo '<a href="Home_FrontEnd.php" style="color: #2E2C2C; text-decoration:none;">A Trip\'s Story</a>';
                } else {
                    // User is not logged in, logo link redirects to Splash.php
                    echo '<a href="Splash.php" style="color: #2E2C2C; text-decoration:none;">A Trip\'s Story</a>';
                }
            ?>
        </div>
        <div class="menu" onclick="toggleMenu()">☰</div>
        <?php include 'Feature_Translator.html'; ?>
        <div class="nav" id="nav">
            <?php
                    // Check if the user is logged in or not
                    if (isset($_SESSION["user_id"])) {
                        // User is logged in, show appropriate links
                        echo '<a href="Home_FrontEnd.php"><i class="fa fa-home"></i> Home</a>';
                        echo '<a href="Profile_FrontEnd.php"><i class="fa fa-user-circle"></i> Profile</a>';
                        echo '<a href="Wishlist_FrontEnd.php"><i class="fa fa-heart"></i> Wishlist</a>';
                        echo '<a href="ContactUs_FrontEnd.php"><i class="fa fa-envelope" style="color:#2E2C2C;"></i> Contact us</a>';
                        echo '<a href="AboutUs_FrontEnd.php"><i class="fa fa-info-circle" style="color: #2E2C2C;"></i> About us</a>';
                        echo '<a href="SignOut_BackEnd.php"><i class="fa fa-sign-out"></i> Sign-out</a>';
                    } else {
                        // User is not logged in, show basic links
                        echo '<a href="SignIn_FrontEnd.php" id="buttoninheader" style="text-decoration: none; margin-right: 10px;"><i class="fa fa-sign-in"></i>  Sign in</a>';
                        echo '<a href="SignUp_FrontEnd.php" id="buttoninheader" style="text-decoration: none; margin-left: 10px;"><i class="fa fa-user-plus"></i>Sign up</a>';
                        echo '<a href="ContactUs_FrontEnd.php"><i class="fa fa-envelope" style="color:#2E2C2C;"></i> Contact us</a>';
                        echo '<a href="AboutUs_FrontEnd.php"><i class="fa fa-info-circle" style="color: #2E2C2C;"></i> About us</a>';
                    }
                ?>
        </div>
    </div>
    <script>
        function toggleMenu() {
            console.log("toggleMenu function called");
            const nav = document.getElementById('nav');
            nav.classList.toggle('active');
        }
    </script>
    <script>
        const header = document.getElementById('header');
        const scrollThreshold = 200; // Adjust this value as needed

        // Function to toggle the fixed class based on scroll position
        function toggleHeaderPosition() {
            if (window.pageYOffset >= scrollThreshold) {
                header.classList.add('fixed-header');
            } else {
                header.classList.remove('fixed-header');
            }
        }
        // Listen for the scroll event and call the toggleHeaderPosition function
        window.addEventListener('scroll', toggleHeaderPosition);
    </script>
</body>

</html>