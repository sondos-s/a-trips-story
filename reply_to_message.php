<?php
session_start();

// Include your database connection file (UDB.php)
include 'UDB.php';


// Check if the user is logged in and is the owner
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] != 9) {
    header("Location: login.php"); // Redirect to login page if not logged in or not the owner
    exit();
}
$isOwner = false;
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 9) {
    $isOwner = true;
}
// Initialize the message variable
$message = "";

// Check if a message ID is provided in the query string
if (isset($_GET["id"])) {
    $messageID = $_GET["id"];

    // Fetch the selected message from the database
    $sql = "SELECT * FROM messages WHERE id = $messageID";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $message = $row["message"];
    } else {
        // Handle message not found
        $message = "Message not found.";
    }
} else {
    // Handle missing message ID
    $message = "Message ID is missing.";
}

// Handle form submission to process the reply
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reply = $_POST["reply"];
    
    // Insert the reply into the database
    $sql = "UPDATE messages SET reply = '$reply' WHERE id = $messageID";
    
    if ($conn->query($sql) === TRUE) {
        // Reply successfully stored
        $message = "Reply sent successfully.";
        
        // Redirect back to messages.php after sending the reply
        header("Location: messages.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<!-- Include your header/navigation here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include your head content here -->
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

    <title>Reply to Messages</title>
    <style>
        
        /* Center the paragraph horizontally and vertically */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 1000vh; /* This ensures the content is centered in the viewport */
            margin: 0; /* Remove default margin to center properly */
        }

        /* Additional styles for the paragraph */
        p {
            text-align: center;
            font-size: 25px;

        }
        h1{
          text-align: center;
            font-size: 40px;
           
        }
     
        </style>
    <?php 
    if ($isOwner) {
        include 'Header_Owner.php'; // Include owner header if the user is an owner
    } else {
        include 'Header.php'; // Include regular user header
    }
?>
</head>



<form method="post" action="" style="max-width: 400px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
    
<h1>Reply to Message</h1>
<br> <br><br> 
<p>Original Message: <?php echo $message; ?></p>

        <label for="reply">Reply:</label>
        <textarea name="reply" id="reply" rows="4" cols="50" required style="width: 100%; padding: 10px;"></textarea>
    </div>
    <div style="text-align: center;">
        <input type="submit" value="Reply" style="background-color: #007bff; color: #fff; padding: 10px 20px; border: none; cursor: pointer;">
    </div>
</form>

</body>
</html>