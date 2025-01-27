<?php
session_start();

// Include your database connection file (UDB.php)
include 'UDB.php';

// Check if the user is logged in and is the owner
$isOwner = false;
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 9) {
    $isOwner = true;
}

// Check if the user_id parameter is set
if (isset($_GET["user_id"])) {
    $userId = $_GET["user_id"];

    // Query to fetch the user's full name
    $userSql = "SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName
                FROM users AS u
                WHERE u.id = $userId";
    $userResult = $conn->query($userSql);

    if ($userResult->num_rows > 0) {
        $userRow = $userResult->fetch_assoc();
        $fullName = $userRow["fullName"];
    } else {
        $fullName = "User Not Found"; // Handle the case where the user is not found
    }

    // Query to fetch messages for the specified user
    $sql = "SELECT m.id, m.message, m.date_sent, m.reply, CONCAT(u.firstName, ' ', u.lastName) AS fullName
            FROM messages AS m
            LEFT JOIN users AS u ON m.user_id = u.id
            WHERE m.user_id = $userId
            ORDER BY m.date_sent DESC";

    $result = $conn->query($sql);
} else {
    // Handle the case where user_id parameter is not set
    header("Location: messages.php"); // Redirect to the messages.php page or handle it differently
    exit;
}
?>

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
    <title>View User Messages</title>
    
    <?php 
    if ($isOwner) {
        include 'Header_Owner.php'; // Include owner header if the user is an owner
    } else {
        include 'Header.php'; // Include regular user header
    }
?>
<style>
    /* Style for the popup */
  /* Style for the popup */
    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        overflow-y: auto; /* Add vertical scrolling */
    }

    .popup-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        max-width: 80%; /* Set a maximum width */
        max-height: 80%; /* Set a maximum height */
        overflow: auto; /* Add scrolling for content that exceeds the max height */
    }

    .close-popup {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }
    /* Style for making the table responsive */
    .responsive-table {
        width: 100% !important;;
        overflow-x: auto !important;;
    }

    /* Set a maximum width for the message column */
    .responsive-table th:first-child,
    .responsive-table td:first-child {
        max-width: 400px !important; /* Apply !important to make it more specific */
        white-space: nowrap ;
        overflow: hidden ;
        text-overflow: ellipsis !important;;
    }

    /* Style for table rows */
    .responsive-table tbody tr {
        height: auto ;
    }

     /* Style for the message cell */
     .message-cell {
        max-width: 100%;
        width: 1%;
        vertical-align: top;
    }

    /* Style for the message text */
    .message-text {
        width: 100%;
        white-space: nowrap;
        overflow-x: auto;
    }
</style>

<script>
  function showReplyPopup(reply) {
    var popup = document.getElementById('reply-popup');
    var popupContent = document.getElementById('popup-content');
    var closeBtn = document.getElementById('close-popup');

    // Set the reply content in the popup
    popupContent.innerHTML = reply;

    // Show the popup
    popup.style.display = 'block';

    // Add a class to the popup content to adjust its size
    popupContent.classList.add('adjust-popup-size');

    // Close the popup when the close button is clicked
    closeBtn.addEventListener('click', function () {
        popup.style.display = 'none';
    });

    // Close the popup when clicking outside of it
    window.addEventListener('click', function (event) {
        if (event.target == popup) {
            popup.style.display = 'none';
        }
    });
}

    function openReplyPopup(messageID) {
    var popup = document.getElementById('reply-popup');
    var popupContent = document.getElementById('popup-content');
    var closeBtn = document.getElementById('close-popup');

    // Set the reply content in the popup (empty initially for new reply)
    popupContent.innerHTML = '';

    // Show the popup
    popup.style.display = 'block';

    // Create a reply form in the popup
    popupContent.innerHTML = '<form id="reply-form"><textarea id="reply-textarea" rows="4" cols="50"></textarea><br><input type="submit" value="Submit Reply"></form>';

    // Handle form submission
    var replyForm = document.getElementById('reply-form');
    replyForm.onsubmit = function(event) {
        event.preventDefault();

        // Get the reply content from the textarea
        var replyContent = document.getElementById('reply-textarea').value;

        // Perform an AJAX request to insert the reply into the database
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'insert_reply.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Handle the response (e.g., display a success message)
                alert("Reply submitted successfully!");
                // Redirect to view_user_messages.php after displaying the success message
            window.location.href = "view_user_messages.php";
            }
        };
        xhr.send('message_id=' + messageID + '&reply=' + encodeURIComponent(replyContent));

        // Close the popup after submitting the reply
        popup.style.display = 'none';
    };

    // Close the popup when the close button is clicked
    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    // Close the popup when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target == popup) {
            popup.style.display = 'none';
        }
    });
}

</script>
</head>
<body>
<div class="responsive-table">
    <?php if (isset($_GET["user_id"])): ?>
        <!-- Display messages for the selected user -->
        <h1 class="messages-h">Messages for User</h1>
        <h2><?php echo $fullName; ?></h2>
        <table class="responsive-table">
    <thead>
        <tr>
            <th>Message</th>
            <th>Date Sent</th>
            <?php if ($isOwner): ?>
                <th>Action</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        // Loop through the messages for the selected user
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messageID = $row["id"];
                $message = $row["message"];
                $dateSent = $row["date_sent"];
                $reply = $row["reply"];

                echo '<tr>';
                echo '<td class="message-cell"><div class="message-text">' . $message . '</td>';
                echo '<td>' . $dateSent . '</td>';

                if ($isOwner) {
                    // Check if there is a reply
                    if (!empty($reply)) {
                        // If there is a reply, show "View Reply" and display the reply in a popup
                        echo '<td><a href="javascript:void(0);" onclick="showReplyPopup(`' . $reply . '`)">View Reply</a></td>';
                    } else {
                        // If there is no reply, provide a link to reply to this message
                        echo '<td><a href="javascript:void(0);" onclick="openReplyPopup(' . $messageID . ')">Reply</a></td>';
                    }
                }

                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4">No messages found for this user.</td></tr>';
        }
        ?>
    </tbody>
</table>



    <?php endif; ?>
</div>

<!-- Popup container for displaying the reply -->
<div id="reply-popup" class="popup">
    <div id="popup-content" class="popup-content adjust-popup-size">
        <!-- Reply content will be displayed here -->
        <span id="close-popup" class="close-popup">&#x2716;</span> <!-- Close button -->
    </div>
</div>

<!-- Add this div for displaying success message -->
<div id="success-message" style="display: none; color: green; margin-top: 10px;">
    Reply submitted successfully!
</div>

<!-- Include your footer here -->
</body>
</html>