<?php
session_start();

// Include your database connection file (UDB.php)
include 'UDB.php';

// Check if the user is logged in and is the owner
$isOwner = false;
if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 9) {
    $isOwner = true;
}

// Query to fetch a list of unique users who have sent messages and their additional information
$userSql = "SELECT DISTINCT u.id AS user_id, 
                        CONCAT(u.firstName, ' ', u.lastName) AS fullName,
                        u.phoneNumber,
                        u.emailAddress,
                        (SELECT MAX(date_sent) FROM messages WHERE user_id = u.id) AS lastMessageDate,
                        CASE WHEN SUM(CASE WHEN m.reply IS NOT NULL AND m.reply <> '' THEN 1 ELSE 0 END) = COUNT(*) THEN 'yes' ELSE 'no' END AS isReplied
            FROM users AS u
            LEFT JOIN messages AS m ON u.id = m.user_id
            GROUP BY u.id
            HAVING COUNT(m.id) > 0"; // Only include users who have sent messages
$userResult = $conn->query($userSql);


// Query to fetch messages from the database, joining with users to get the user's full name
$sql = "SELECT m.id, m.message, m.date_sent, m.user_id, m.reply, CONCAT(u.firstName, ' ', u.lastName) AS fullName
        FROM messages AS m
        LEFT JOIN users AS u ON m.user_id = u.id
        ORDER BY m.date_sent DESC";

$result = $conn->query($sql);
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
    <title>Messages</title>
    
    <?php 
    if ($isOwner) {
        include 'Header_Owner.php'; // Include owner header if the user is an owner
    } else {
        include 'Header.php'; // Include regular user header
    }
?>
<style>
 
</style>
</head>
<body>
<div class="table-container">
    <h1 class="messages-h">Messages</h1>

    <!-- Table to display users who have sent messages -->
   <!-- Table to display users who have sent messages -->
<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Phone Number</th>
            <th>Email</th>
            <th>Last Message Date</th>
            <th>Is Replied</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Loop through the unique users
        while ($userRow = $userResult->fetch_assoc()) {
            $userId = $userRow["user_id"];
            $fullName = $userRow["fullName"];
            $phoneNumber = $userRow["phoneNumber"];
            $email = $userRow["emailAddress"];
            $lastMessageDate = $userRow["lastMessageDate"];
            $isReplied = $userRow["isReplied"];
 // Define the inline style based on the "isReplied" value
 $rowStyle = ($isReplied === 'no') ? 'background-color: rgb(255, 255, 153);' : '';
 echo '<tr style="' . $rowStyle . '">';
            echo '<td><a href="view_user_messages.php?user_id=' . $userId . '">' . $fullName . '</a></td>';
            echo '<td>' . $phoneNumber . '</td>';
            echo '<td>' . $email . '</td>';
            echo '<td>' . $lastMessageDate . '</td>';
            echo '<td>' . $isReplied . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>


    <?php if (isset($_GET["user_id"])): ?>
        <!-- Display messages for the selected user -->
        <h2>Messages for <?php echo $_GET["user_id"]; ?></h2>
        <table>
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
                while ($row = $result->fetch_assoc()) {
                    if ($row["user_id"] == $_GET["user_id"]) {
                        $messageID = $row["id"];
                        $message = $row["message"];
                        $dateSent = $row["date_sent"];
                        $reply = $row["reply"];

                        echo '<tr>';
                        echo '<td>' . $message . '</td>';
                        echo '<td>' . $dateSent . '</td>';

                        if ($isOwner) {
                            // Check if there is a reply
                            if (!empty($reply)) {
                                // If there is a reply, show "View Reply" and display the reply
                                echo '<td><a href="view_reply.php?id=' . $messageID . '">View Reply</a></td>';
                            } else {
                                // If there is no reply, provide a link to reply to this message
                                echo '<td><a href="reply_to_message.php?id=' . $messageID . '">Reply</a></td>';
                            }
                        }

                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
<!-- Include your footer here -->
</body>
</html>
