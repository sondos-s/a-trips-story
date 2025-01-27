<?php
session_start();

// Include your database connection file (UDB.php)
include 'UDB.php';
// Check if the user is logged in and is the owner
if (!isset($_SESSION["user_id"]) || $_SESSION["user_id"] != 9) {
    header("Location: login.php"); // Redirect to login page if not logged in or not the owner
    exit();
}

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
        header("Location: messages.php");
        exit();
    }
} else {
    // Handle missing message ID
    header("Location: messages.php");
    exit();
}
?>

<!-- Include your header/navigation here -->

<h1>Reply to Message</h1>

<p>Original Message: <?php echo $message; ?></p>

<form method="post" action="process_reply.php">
    <textarea name="reply" rows="4" cols="50" required></textarea>
    <input type="hidden" name="message_id" value="<?php echo $messageID; ?>">
    <input type="submit" value="Reply">
</form>

<!-- Include your footer here -->
<!-- Include your footer here -->