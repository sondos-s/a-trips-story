<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . "/UDB.php";
if (isset($_POST['reviewId'])) {
    $reviewId = $_POST['reviewId'];

        $sql = "DELETE FROM reviews WHERE reviewId = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $reviewId);

        if ($stmt->execute()) {
            // Redirect back to the previous page or to a success page
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            // Handle error - for example, print an error message
            echo "Error deleting review: " . $stmt->error;
        }
        
        $stmt->close();
        $mysqli->close();
    }
?>
