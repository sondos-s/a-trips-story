<?php
session_start();

// Check if the user is logged in and get their user ID from the session
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page or display an error message
    header("Location: login.php");
    exit(); // Stop script execution
}

// db connection
include 'UDB.php';

// Get the user's ID from the session
$userID = $_SESSION["user_id"];

// Query to retrieve the user's messages
$sql = "SELECT id, message, date_sent, reply FROM messages WHERE user_id = $userID";
$result = $conn->query($sql);

// Initialize variables for form data and popup message
$message = "";
$reply = "";
$popupMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $message = htmlspecialchars($_POST["message"]); // HTML-encode user input
    $userID = $_SESSION["user_id"]; // Get the user's ID from the session

    // You should validate and sanitize user input here

    // Insert the message into the database using prepared statements
    $sql = "INSERT INTO messages (message, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $message, $userID);

    if ($stmt->execute()) {
        // Set the success popup message
        $popupMessage = "Message has been sent.";
        // Redirect to prevent accidental resubmission
        header("Location: messages_customer.php");
        exit();
    } else {
        // Handle the database error, if any
        $error_message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   
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
    <title>Messages</title>
    
    <?php 
   
   
        include 'Header.php'; // Include regular user header
    
?>
   
    <style>
        .container-message-customer2 {
    margin: 20px auto; /* Center the container horizontally */
    max-width: 70%; /* Set a maximum width as a percentage of the viewport width */
    max-height:70%;
}

.message-container2 {
    margin: 20px auto; /* Center the message container */
    padding: 10px;
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    overflow-y: auto; /* Add vertical scroll when content exceeds the container height */
    max-height: 80vh; /* Set a maximum height of 80% of the viewport height */
    width: 100%; /* Adjust the width to 100% to use the entire available width */
}

.page-container {
    padding: 20px; /* Add padding as needed */
    max-height: 100%;
   max-width: 100%;
    font-family: Arial, sans-serif; /* Specify the font family for the entire page */
}





        .message-text {
            max-width: 100%; /* Ensure the text doesn't overflow the container */
        }

        /* Symmetrical Send a Message container */
        .send-message-container {
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #ddd;
            margin: 20px auto; /* Center the send message container */
            width: 80%; /* Adjust the width as needed */
        }

        /* Center the Send button */
        .send-message-container button {
            display: block;
            margin: 0 auto;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .send-message-container button:hover {
            background-color: #45a049;
        }
        /* Style for message content */
.message-content {
    font-weight: bold; /* Example: Make the message content text bold */
    color: #333; /* Example: Change the text color */
    margin-bottom: 10px; /* Example: Add margin to separate messages */
}

/* Style for date sent */
.date-sent {
    font-style: italic; /* Example: Make the date italic */
    color: #777; /* Example: Change the text color */
}

/* Style for reply content */
.reply-content {
    font-weight: bold; /* Example: Make the reply content text bold */
    color: #008000; /* Example: Change the text color */
    margin-top: 10px; /* Example: Add margin to separate replies */
}

/* Style for the "No messages found" message */
.no-messages {
    font-style: italic; /* Example: Make it italic */
    color: #888; /* Example: Change the text color */
}

/* Style for the success message */
.success-message {
    color: #4CAF50; /* Example: Set a success message color */
    font-weight: bold; /* Example: Make it bold */
}
#page-body {
            background-color: #f5f5f5; /* Set background color for the body */
            font-family: Arial, sans-serif; /* Specify the font family for the entire page */
        }
    </style>
    <!-- ... Your existing JavaScript and PHP code ... -->
</head>
<body id="page-body-messages">
<div class="page-container">
<div class="container-message-customer2">
    <h1 style="text-align: center; font-size: 40px; margin-top:100px">Messages</h1>

    <!-- Display any error messages, if applicable -->
    <?php if (isset($error_message)) { ?>
        <p><?php echo $error_message; ?></p>
    <?php } ?>

    <h2 style=" font-size: 20px; margin-top:100px">Your Messages</h2>

    <!-- Display messages and replies -->
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='message-container2' id='message-" . $row["id"] . "'>";
            echo "<div class='message-text'>";
            echo "<p class='message-content'><strong>Message:</strong> " . $row["message"] . "</p>";
            echo "</div>";

            echo "<p class='date-sent'><strong>Date Sent:</strong> " . date("Y-m-d H:i", strtotime($row["date_sent"])) . "</p>";

            // Check if there is a reply and display it in a reply container
            if (!empty($row["reply"])) {
                echo "<div class='reply-container'>";
                echo "<p class='reply-content'><strong>Reply:</strong> " . $row["reply"] . "</p>";
                echo "</div>";
            }

            echo "</div>"; // Close the message container
        }
    } else {
        echo "<p class='no-messages'>No messages found.</p>";
    }
    ?>

    <h2 style=" font-size: 20px; margin-top:100px">Send a Message</h2>

    <!-- Create the message form -->
    <div class="send-message-container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="message">Message:</label><br>
            <textarea id="message" name="message" rows="4" cols="50" required><?php echo $message; ?></textarea><br><br>

            <button type="submit">Send</button>
        </form>
    </div>
</div>
</div>
<!-- Display the success popup message -->
<?php if (!empty($popupMessage)) { ?>
    <div class="popup">
        <p class='success-message'><?php echo $popupMessage; ?></p>
    </div>
<?php } ?>

<!-- Add your footer here -->
</body>
</html>
