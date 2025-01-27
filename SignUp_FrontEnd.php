<!DOCTYPE html>
<html>

<head>
	<title>Sign Up</title>
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
	<div class="container">
		<p style="font-size: 18px; font-family: 'Jost'; color: red; font-weight: bold; position: relative; z-index: 999;">
		</p>
		<form action="SignUp_BackEnd.php" method="post" onsubmit="return validateEmail()">
			<h3>Sign Up</h3>
			<br>
			<span id="validationMessages" style="font-size: 14px; color: red; padding-left: 70px; font-family: \'Courier New\', Courier, monospace;"></span>
			<?php
			// Check if the form has been submitted again
			if (isset($_POST['submit'])) {
				unset($_SESSION['error_message']); // Clear the error message from the session
			}

			// Check if there is an error message in the session
			if (isset($_SESSION['error_message'])) {
				echo '<div class="error-message">';
				echo '<span style="color: red; font-family: \'Courier New\', Courier, monospace; padding-left: 70px; float: left;"></span> ' . $_SESSION['error_message'];
				echo '</div>';

				// Clear the error message from the session
				unset($_SESSION['error_message']);
			}
			?>
			<br>
			<div class="form-group">
				<div class="float-left">

					<!-- FirstName -->
					<label for="first-name">First Name <span class="icon" onmouseover="showMessage('first-name-message')" onmouseout="hideMessage('first-name-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span></label>
					<input type="text" id="first-name" name="first_name" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter your first name">
					<div id="first-name-message" class="speech-bubble">Name must contain only letters</div>

					<!-- Last Name -->
					<label for="last-name">Last Name <span class="icon" onmouseover="showMessage('last-name-message')" onmouseout="hideMessage('last-name-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span></label>
					<input type="text" id="last-name" name="last_name" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter your last name">
					<div id="last-name-message" class="speech-bubble"> name must contain only letters</div>

					<!-- BirthDate -->
					<label for="birthdate">Birthdate</label>
					<input type="date" id="birthdate" name="birthdate" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter your birth-date" onblur="validateAge()">
					<p id="age-error" style="font-size: 13px; font-family: 'Jost'; color: red; font-weight: bold; position: relative; z-index: 999;"></p>

					<!-- City -->
					<label for="city">City:</label>
					<select name="city" id="city">
						<?php
						include 'UDB.php';
						// Fetch city names from the database and populate the dropdown
						$sql = "SELECT cityName FROM cities";
						$result = $mysqli->query($sql);
						while ($row = $result->fetch_assoc()) {
							echo "<option value='" . $row["cityName"] . "'>" . $row["cityName"] . "</option>";
						}
						?>
					</select>

					<!-- PhoneNumber -->
					<label for="phone-number">Phone Number</label>
					<input type="text" id="phone-number" name="phoneNumber" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter your phone number">
					
				</div>


				<div class="float-left">

					<!-- Username -->
					<label for="username">Username</label>
					<input type="text" id="username" name="username" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter your username">
					<span id="username-message" class="validation-message" style="color: red;"></span>

					<!-- Password -->
					<label for="password">Password <span class="icon" onmouseover="showMessage('password-message')" onmouseout="hideMessage('password-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span></label>
					<input type="password" id="password" name="password" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter a password">
					<div id="password-message" class="speech-bubble">Password must be at least 8 characters, contain at least one letter, and at least one number</div>


					<!-- ConfirmPassword -->
					<label for="confirm-password">Confirm Password<span style="position:relative; left:8px; top:-1px;" class="icon" onmouseover="showMessage('confirm-password-message')" onmouseout="hideMessage('confirm-password-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span></label>
					<input type="password" id="confirm-password" name="confirm_password" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Confirm your password">
					<div id="confirm-password-message" class="speech-bubble">Passwords must match</div>

					<!-- EmailAddress -->
					<label for="email">Email Address</label><span style="position:relative; left:111px; top:-35px;" class="icon" onmouseover="showMessage('email-message')" onmouseout="hideMessage('email-message')"><i class="fa fa-question-circle" style="color: #a9809e;"></i></span></label>
					<input type="email" id="email" name="email" class="form-control" required="" style="height: 30px; width: 200px;" placeholder="Enter your e-mail">
					<div id="email-message" class="speech-bubble">Please enter a valid email address</div>

					<!-- EnableNotifications -->
					<label for="notification">Enable Email Notifications</label>
					<input type="checkbox" name="enableNotification" id="enableNotification" value="1">
					<p style="font-size: 12px; font-family: 'Jost'; color: black; font-weight: lighter; position: relative; z-index: 999;">
					To stay up to date with our latest updates.
					</p>

				</div>
			</div>
			<button type="submit" class="signbutton" id="registerButton">Register</button>
			<div class="unvisiblediv" style="margin-top: 100px;">
				<h2>&nbsp;&nbsp;&nbsp;</h2>
			</div>
		</form>
	</div>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<br><br>
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
	function showMessage(messageId) {
		var message = document.getElementById(messageId);
		message.style.display = 'block';
	}

	function hideMessage(messageId) {
		var message = document.getElementById(messageId);
		message.style.display = 'none';
	}

	// Check if the email address is valid and display the message
	document.getElementById('email').addEventListener('input', function() {
		var email = this.value;
		var message = document.getElementById('email-message');

		// Regular expression for validating email addresses
		var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		if (emailPattern.test(email)) {
			message.style.color = 'green';
			message.textContent = 'Email is valid';
		} else {
			message.style.color = 'red';
			message.textContent = 'Please enter a valid email address';
		}
	});
	var passwordField = document.getElementById('password');
	var confirmPasswordField = document.getElementById('confirm-password');
	var originalBorderColor = confirmPasswordField.style.borderColor; // Store the original border color

	var confirmFieldClicked = false; // Flag to track if Confirm Password field is clicked

	// Function to check if passwords match and update field color
	function checkPasswordMatch() {
		if (confirmFieldClicked) {
			var password = passwordField.value;
			var confirmPassword = confirmPasswordField.value;
			var message = document.getElementById('confirm-password-message');

			if (password === confirmPassword && password !== '') {
				confirmPasswordField.style.borderColor = 'green'; // Set the border color to green
				message.style.color = 'green';
				message.textContent = 'Passwords match';
			} else {
				confirmPasswordField.style.borderColor = 'red'; // Set the border color to red
				message.style.color = 'red';
				message.textContent = 'Passwords must match';
			}
		}
	}

	// Add focus and blur event listeners to track when the fields are clicked
	confirmPasswordField.addEventListener('focus', function() {
		if (!confirmFieldClicked) {
			confirmPasswordField.style.borderColor = originalBorderColor; // Reset to original color on focus if not already clicked
			confirmFieldClicked = true; // Set the flag when Confirm Password is clicked
		}
	});

	confirmPasswordField.addEventListener('blur', function() {
		if (!confirmPasswordField.value && confirmFieldClicked) {
			confirmPasswordField.style.borderColor = originalBorderColor; // Reset to original color on blur if no input and clicked
		}
		checkPasswordMatch();
	});

	// Initial check when the page loads
	checkPasswordMatch();
	// Function to calculate age from birthdate
	function calculateAge(birthdate) {
		const today = new Date();
		const dob = new Date(birthdate);
		const age = today.getFullYear() - dob.getFullYear();
		const monthDiff = today.getMonth() - dob.getMonth();

		if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
			return age - 1;
		}

		return age;
	}

	// Function to validate the birthdate and show/hide error message
	function validateAge() {
		const birthdateInput = document.getElementById('birthdate');
		const ageError = document.getElementById('age-error');
		const birthdate = birthdateInput.value;

		const age = calculateAge(birthdate);

		if (age < 18) {
			ageError.textContent = 'You must be at least 18';
			ageError.style.display = 'block';
			return false;
		} else {
			ageError.style.display = 'none';
			return true;
		}
	}

	// Add an event listener to the form submit button
	document.querySelector('form').addEventListener('submit', function(e) {
		if (!validateAge()) {
			e.preventDefault(); // Prevent the form from submitting if age is less than 18
		}
	});
</script>

<script>
	// Function to check if the email address is valid and belongs to a specified domain
	function validateEmail() {
		const emailInput = document.getElementById('email');
		const email = emailInput.value.toLowerCase();
		const message = document.getElementById('email-message');

		// Regular expression for validating email addresses
		const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		// List of allowed email domains
		const allowedDomains = [
		'gmail.com',
		'hotmail.com',
		'outlook.com',
		'yahoo.com',
		'aol.com',
		'icloud.com',
		'mail.com',
		'protonmail.com',
		'zoho.com',
		'yandex.com',
		'gmail.co.il',
		'hotmail.co.il',
		'yahoo.co.il',
		'walla.co.il',
		'netvision.net.il',
		'012.net.il',
		'bezeqint.net',
		'bezeq.co.il',
		'zahav.net.il',
		'013.net.il',
		'smile.net.il',
		'nana.co.il',
		'barak.net.il',
		'internet-zahav.net',
		'inter.net.il',
		'bezeqint.co.il',];

		if (emailPattern.test(email)) {
        // Split the email address to get the domain part
        const parts = email.split('@');
        if (allowedDomains.includes(parts[1])) {
            return true; // Return true to allow form submission
        }
    }

    // If the email is invalid or not from an allowed domain, prevent form submission
    alert('Please enter a valid email address from an allowed domain.');
    return false; // Return false to prevent form submission
}
</script>

<script>
document.getElementById('registerButton').addEventListener('click', function (event) {
    // Prevent the form from submitting initially
    event.preventDefault();

    // Get the username input value
    var username = document.getElementById('username').value;

    // Perform all validation checks here
    var firstName = document.getElementById('first-name').value;
    var lastName = document.getElementById('last-name').value;
    var email = document.getElementById('email').value;
    var phoneNumber = document.getElementById('phone-number').value;
    var birthdate = document.getElementById('birthdate').value;
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm-password').value;

    // Check if any of the required fields are empty
    if (
        firstName.trim() === '' ||
        lastName.trim() === '' ||
        email.trim() === '' ||
        username.trim() === '' ||
        phoneNumber.trim() === '' ||
        birthdate.trim() === '' ||
        password.trim() === '' ||
        confirmPassword.trim() === ''
    ) {
        displayValidationMessages('Please fill in all required fields.');
        return;
    }

    // Check if the first name contains only letters
    if (!/^[A-Za-z]+$/.test(firstName)) {
        displayValidationMessages('First Name must contain only letters.');
        return;
    }

    // Check if the last name contains only letters
    if (!/^[A-Za-z]+$/.test(lastName)) {
        displayValidationMessages('Last Name must contain only letters.');
        return;
    }

    // Check if the username has at least 4 letters
    if (username.length < 4) {
        displayValidationMessages('Username must be at least 4 letters.');
        return;
    }

    // Check if the passwords match
    if (password !== confirmPassword) {
        displayValidationMessages('Passwords must match.');
        return;
    }

    // Check if the age is at least 18
    if (!validateAge()) {
        return;
    }

    // Check if the email address is valid and belongs to an allowed domain
    if (!validateEmail()) {
        return;
    }

	// Check if the phone number has at least 10 digits
	if (phoneNumber.length < 10) {
        displayValidationMessages('Please enter a valid phone number.');
        return;
    }
	

    // Send an AJAX request to check the username availability
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'UsernameAvailability.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Define the callback function for the AJAX request
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                // The AJAX request was successful
                if (xhr.responseText === 'taken') {
                    // Username is taken, display an alert
                    alert('Username is already taken. Please choose a different username.');
                } else {
                    // Username is available, proceed with form submission
                    document.querySelector('form').submit();
                }
            } else {
                // Handle AJAX request error here
                alert('Error checking username availability.');
            }
        }
    };

    // Send the request with the username as data
    xhr.send('username=' + username);
});

// Function to display validation messages
function displayValidationMessages(message) {
    const validationMessages = document.getElementById('validationMessages');
    validationMessages.textContent = message;
}
</script>

