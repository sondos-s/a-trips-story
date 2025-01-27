<?php include 'ResetPassword_Backend.php'; ?>
<?php session_start(); ?>

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

	.error-message {
		color: red;
		text-align: center;
		padding: 10px;
		font-size: 12px;
		border: 1px solid red;
		background-color: #ffe6e6;
	}
</style>

<body>
	<?php include 'Header.php'; ?>
	<div class="container-in">
		<form class="form-in-cp" method="post" action="change_password_backend.php">
			<center>
				<h3 style="font-size: 20px;">Change Password</h3>
				<br><br>
			</center>
			<!-- HTML structure -->
			<?php if (isset($_SESSION['error_message'])) : ?>
				<script>
					alert('<?php echo $_SESSION['error_message']; ?>');
				</script>
				<?php unset($_SESSION['error_message']); // To clear the error message after displaying it 
				?>
			<?php endif; ?>
			<div class="form-group-in-cp">
				<label for="current_password" style="font-size: 14px; font-weight: lighter; padding-left: 90px;">Current Password:</label>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="password" id="current_password" name="current_password" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter your current password">
			</div>
			<div class="form-group-in-cp">
				<!-- Password -->
				<label for="password" style="font-size: 14px; font-weight: lighter; padding-left: 90px;">New Password:<span style="left: 30px;" class="icon" onmouseover="showMessage('password-message')" onmouseout="hideMessage('password-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span></label>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="password" id="password" name="password" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter a password">
				<div id="password-message" class="speech-bubble" style="font-size: 12px; font-weight: lighter;">Password must be at least 8 characters, contain at least one letter, and at least one number</div>
			</div>
			<div class="form-group-in-cp">
				<!-- ConfirmPassword -->
				<label for="confirm-password" style="font-size: 14px; font-weight: lighter; padding-left: 90px;">Confirm New Password:</label>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="password" id="confirm-password" name="confirm_password" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Confirm your password"><span style="position:relative; left:30px; top:-16px;" class="icon" onmouseover="showMessage('confirm-password-message')" onmouseout="hideMessage('confirm-password-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span>
				<div id="confirm-password-message" class="speech-bubble" style="font-size: 12px; font-weight: lighter;">Passwords must match</div>
			</div>
			<br>
			<input class="submitcpbtn" type="submit" value="Submit">
		</form>
		<br><br><br>
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