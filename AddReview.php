
<?php
 session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Database connection (modify with your own connection details)
    $mysqli = require __DIR__ . "/UDB.php";

// Check if the connection was successful
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    if (isset($_GET['trip_id'])) {
        $tripId = $_GET['trip_id'];
        $userId = $_SESSION['user_id'];

// Retrieve form data
        $reviewText = $mysqli->real_escape_string($_POST["review"]);
        $rating = intval($_POST["rating"]); // Convert to integer
// Process uploaded pictures (you can store file paths in the database)
        $picturePaths = array();
        foreach ($_FILES["pictures"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["pictures"]["tmp_name"][$key];
                $name = basename($_FILES["pictures"]["name"][$key]);
                $uploadDir = "uploads/"; // Specify your upload directory
                $path = $uploadDir . $name;
                move_uploaded_file($tmp_name, $path);
                $picturePaths[] = $path;
                
            }
        }

        $picturePathsString = json_encode($picturePaths);
     
       

// Insert data into the database using prepared statements
        $insertQuery = "INSERT INTO reviews (tripId, userId, review, rate, picture_paths) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insertQuery);

// Check if the statement was prepared successfully
        if ($stmt) {
            $stmt->bind_param("iisss", $tripId, $userId, $reviewText, $rating, $picturePathsString);
            if ($stmt->execute()) {
                // Successful insertion
                echo "Review submitted successfully.";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                // Handle insertion error
                echo "Error: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            // Handle statement preparation error
            echo "Error preparing statement: " . $mysqli->error;
        }
    }
}


