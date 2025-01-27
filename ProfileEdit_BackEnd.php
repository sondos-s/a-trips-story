<?php
session_start();
$userId = $_SESSION["user_id"];

include "UDB.php";

// Check if the database connection is successful
if (!$mysqli) {
    die("Database connection error: " . $mysqli->connect_error);
}

// Retrieve user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Prepare statement error: " . $mysqli->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$userDetails = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input fields
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $username = $_POST["username"];
    $phoneNumber = $_POST["phoneNumber"];
    $email = $_POST["emailAddress"];
    $birthdate = $_POST["birthDate"];
    $cityId = $_POST["city"];

    $errorMessages = [];

    // Check if username already exists
    if (checkIfExists($mysqli, 'username', $username, $userId)) {
        $errorMessages[] = 'Username is already taken. Please choose a different username.';
    }

    // Check if email already exists
    if (checkIfExists($mysqli, 'emailAddress', $email, $userId)) {
        $errorMessages[] = 'An account with this email address already exists!';
    }

    // If there are any error messages, display them and return
    if (count($errorMessages) > 0) {
        $errorMessageString = implode('\\n', $errorMessages); // Join the error messages with newline character
        echo "<script>alert('$errorMessageString');</script>";
        return;
    }
    

    // Retrieve "enable notification" checkbox value
    $enableNotification = isset($_POST["enableNotification"]) ? 1 : 0;

    // Update user profile
    $updateSql =
        "UPDATE users SET firstName=?, lastName=?, emailAddress=?, birthDate=?, phoneNumber=?, username=?, city=?, enableNotification=? WHERE id=?";
    $updateStmt = $mysqli->prepare($updateSql);

    if (!$updateStmt) {
        die("Prepare statement error: " . $mysqli->error);
    }

    $updateStmt->bind_param(
        "sssssssii",
        $firstName,
        $lastName,
        $email,
        $birthdate,
        $phoneNumber,
        $username,
        $cityId,
        $enableNotification, // Bind "enable notification" value
        $userId
    );

    if ($updateStmt->execute()) {
        // Popup HTML code
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Profile Updated Successfully</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="popup-container" id="popup">
        <div class="popup-content">
            <a href="Profile_FrontEnd.php" class="popup-close">×</a>
            <div><i class="fa fa-check-circle-o fa-3x" id="checkicon"></i></div>
            <h2 class="popup-title" style="color: #65b468;">Profile Updated</h2>
            <br><hr><br>
            <p class="popup-text">Your changes have been saved.</p>
            <br>
            <a href="Profile_FrontEnd.php"><button id="popupokbtn"> OK </button></a>
        </div>
    </div>

    <script>
        window.onload = function () {
            openPopup();
        };

        function openPopup() {
            document.getElementById("popup").classList.add("active");
        }

        function closePopup() {
            document.getElementById("popup").classList.remove("active");
        }
    </script>
</body>

</html>
HTML;
    } else {
        $error = "Error updating profile: " . $updateStmt->error;
    }
}



/**
 * @param mysqli $mysqli MySQLi object
 * @param string $field Field to check (e.g. username, emailAddress, phoneNumber)
 * @param string $value Value to check for existence
 * @param int $userId Current user's ID
 * @return bool True if exists, false otherwise
 */
function checkIfExists($mysqli, $field, $value, $userId) {
    $sql = "SELECT id FROM users WHERE $field = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        die("Prepare statement error: " . $mysqli->error);
    }

    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($existingUserId);
        $stmt->fetch();
        $stmt->close();
        return $existingUserId != $userId;
    }

    $stmt->close();
    return false;
}
?>

