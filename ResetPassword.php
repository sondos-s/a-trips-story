<?php include 'ResetPassword_Backend.php'; ?>

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
<style>
    .info {
      color: #333;
      text-align: center;
      font-size: 24px;
    }
    
    .info a {
      color: #0066cc;
      text-decoration: none;
    }
	.form-group-in {
  display: flex;
  flex-direction: column; 
}
 
  </style>
<body>
<?php include 'Header.php'; ?>
<div class="container-in">
    <?php if ($password_reset_successful) : ?>
        <h1 class="info">Password successfully reset. You can now <a href="SignIn_FrontEnd.php">sign in</a>.</h1>
    <?php else : ?>
        <form class="form-in" method="POST" action="">
            <h3>Reset Password</h3>
            <?php if(isset($errorMessage)): ?>
    <p id="error-message" style="color: red;"><?php echo $errorMessage; ?></p>
<?php endif; ?>
            <div class="form-group-in">
					<!-- Password -->
					<label for="password">Password <span class="icon" onmouseover="showMessage('password-message')" onmouseout="hideMessage('password-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span></label>
					<input type="password" id="password" name="password" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter a password">
					<div id="password-message" class="speech-bubble">Password must be at least 8 characters, contain at least one letter, and at least one number</div>


					<!-- ConfirmPassword -->
					<label for="confirm-password">Confirm Password<span style="position:relative; left:100px; top:-16px;" class="icon" onmouseover="showMessage('confirm-password-message')" onmouseout="hideMessage('confirm-password-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span></label>
					<input type="password" id="confirm-password" name="confirm_password" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Confirm your password">
					<div id="confirm-password-message" class="speech-bubble">Passwords must match</div>
            </div>
            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
            <input class="signinbutton" type="submit" value="Reset Password">
        </form>
    <?php endif; ?>
</div>
</body>
</html>

<script>
	function showMessage(messageId) {
		var message = document.getElementById(messageId);
		message.style.display = 'block';
	}

	function hideMessage(messageId) {
		var message = document.getElementById(messageId);
		message.style.display = 'none';
	}
	// Check if the password meets the criteria and update the password field color
	document.getElementById('password').addEventListener('input', function() {
		var password = this.value;
		var passwordField = document.getElementById('password'); // Get the password field element
		var message = document.getElementById('password-message');

		// Define the password criteria
		var hasLetter = /[a-zA-Z]/.test(password);
		var hasNumber = /\d/.test(password);
		var isLengthValid = password.length >= 8;

		if (isLengthValid && hasLetter && hasNumber) {
			passwordField.style.borderColor = 'green'; // Set the border color to green
			message.style.color = 'green';
			message.textContent = 'Password is valid';
		} else {
			passwordField.style.borderColor = 'red'; // Set the border color to red
			message.style.color = 'red';
			message.textContent = 'Password must be at least 8 characters, contain at least one letter, and at least one number';
		}
	});

	// Check if the passwords match and display the message for confirm password
	document.getElementById('confirm-password').addEventListener('input', function() {
		var password = document.getElementById('password').value;
		var confirmPassword = this.value;
		var message = document.getElementById('confirm-password-message');

		if (password === confirmPassword) {
			message.style.color = 'green';
			message.textContent = 'Passwords match';
		} else {
			message.style.color = 'red';
			message.textContent = 'Passwords must match';
		}
	});

	// Check if the passwords match and display the message for confirm password
	document.getElementById('confirm-password').addEventListener('input', function() {
		var password = document.getElementById('password').value;
		var confirmPassword = this.value;
		var message = document.getElementById('confirm-password-message');

		if (password === confirmPassword) {
			message.style.color = 'green';
			message.textContent = 'Passwords match';
		} else {
			message.style.color = 'red';
			message.textContent = 'Passwords must match';
		}
	});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector(".form-in").addEventListener("submit", function(e) {
        const password = document.querySelector("#password").value;
        const confirmPassword = document.querySelector("#confirm-password").value;
        
        if(password.length < 8 || !/\d/.test(password) || !/[a-zA-Z]/.test(password)) {
            alert("Password must be at least 8 characters long and contain both numbers and letters.");
            e.preventDefault();
            return;
        }
        
        if(password !== confirmPassword) {
            alert("Passwords do not match.");
            e.preventDefault();
        }
    });
});
</script>

