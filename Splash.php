<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Bitter' rel='stylesheet'>
  <title>A Trip's Story</title>
</head>

<body>
  <?php include 'Header.php'; ?>
  <p class="splashtext">Join us & let’s go on a trip !</p>
  <div id="bordersplash">
    <img src="Pictures/img1.jpeg" alt="Image 1" style="visibility: hidden; width: 10px;">
  </div>
  <div class="imganimation">
    <img src="ViewStyles/AnimationImages/img1.jpeg" alt="Image 1" />
    <img src="ViewStyles/AnimationImages/img2.jpeg" alt="Image 2" />
    <img src="ViewStyles/AnimationImages/img3.jpeg" alt="Image 3" />
    <img src="ViewStyles/AnimationImages/img4.jpeg" alt="Image 4" />
    <img src="ViewStyles/AnimationImages/img5.jpeg" />
  </div>
  <!-- Buttons -->
  <div class="buttons">
    <a href="SignUp_FrontEnd.php" style="color: #2E2C2C; text-decoration: none;">
      <button class="button"><i class="fas fa-user-plus"></i> Sign up</a></button>
    <a href="SignIn_FrontEnd.php" style="color: #2E2C2C; text-decoration: none;">
      <button class="button"><i class="fas fa-sign-in-alt"></i> Sign in</a></button>
    <a href="Home_FrontEnd.php" style="color: #2E2C2C; text-decoration: none;">
      <button class="button"><i class="fas fa-user"></i> Continue as visitor</a></button>
  </div>
  <audio id="soundPop">
    <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
  </audio>
</body>
</html>

<script>
    // Function to play the sound
    function playSound() {
        var sound = document.getElementById("soundPop");
        sound.currentTime = 0; // Reset the sound to the beginning
        sound.play();
    }

    // Attach hover event listeners to buttons
    var buttons = document.querySelectorAll('.button');
    buttons.forEach(function(button) {
        button.addEventListener('mouseenter', playSound);
    });
</script>

