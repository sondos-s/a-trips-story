<?php
// Check if the user is logged in (you'll need to implement your own authentication logic)
$isLoggedIn = isset($_SESSION['user_id']); // Assuming you store user ID in a session variable

// Render the HTML code based on whether the user is logged in
?>

<footer style="font-family: Calibri, sans-serif; width: 100%; font-size: 16px; color: black;">
    <div class="footer-container" style="display: flex; justify-content: space-between; margin-top: 400px;  background-color: #d3d1d1; width: 100%; color: black;">
        <div class="footer-column left-column" style="margin-right: 100px;">
            <div class="footer-logo">
                <img src="ViewStyles/Footer/logo.png" alt="Logo" style="width: 67px; height: 60px;">
                <br><br>
                <div class="footer-text" style="float: left;">
                    Copyright &copy; <?php echo date("Y"); ?> QestMeshwar.com™. All rights reserved.
                </div>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
        </div>
        <div class="footer-column center-column" style="margin-right: 20px; margin-left: 10px;">
            <div class="footer-links">
                <ul style="list-style: none;">
                    <li><a href="Home_FrontEnd.php" style="text-decoration: none; color: blue;">Home</a></li>
                    <li><a href="AboutUs_FrontEnd.php" style="text-decoration: none; color: blue;">About us</a></li>
                    <li><a href="ContactUs_FrontEnd.php" style="text-decoration: none; color: blue;">Contact us</a></li>
                    <li><a href="FAQ_FrontEnd.php" style="text-decoration: none; color: blue;">Frequently Asked Questions</a></li>
                    <?php if ($isLoggedIn): ?>
                    <li><a href="messages_customer.php" style="text-decoration: none; color: blue;">Messages</a></li>
                <?php endif; ?>
                </ul>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div class="footer-text-s" style="font-family: 'Montserrat', Arial, sans-serif; font-size: 12px;">
                mariakhouryhkh@gmail.com • sondos.diiab@gmail.com • solyma.mady@hotmail.co.il
            </div>
            <br>
        </div>
        <div class="footer-column right-column" style="margin-left: 40px;">
            <div class="footer-social">
                <ul style="list-style: none;">
                    <li><a href="https://www.facebook.com/profile.php?id=100087866574493"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="https://www.instagram.com/meshwar.beso/?utm_source=ig_embed&ig_rid=e9e17527-61ec-481f-9c35-375ccebd2240"><i class="fa fa-instagram"></i></a></li>
                    <li><i class="fa fa-phone"></i>&nbsp;&nbsp;054-689-9446</li>
                </ul>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
        </div>
    </div>
</footer>
