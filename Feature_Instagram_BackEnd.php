<?php
// Include your database connection file (UDB.php)
require_once 'UDB.php';

// Fetch the last three Instagram post URLs from the database
$sql = "SELECT link FROM instasection ORDER BY linkId DESC LIMIT 3";
$result = $mysqli->query($sql);

if ($result) {
    // Check if there are any links
    if ($result->num_rows > 0) {
        echo "<div class='instagram-container'>"; // Create a container for the posts
        while ($row = $result->fetch_assoc()) {
            $instagramPostURL = $row['link'];
            
            // Generate the Instagram post embed code with custom styling
            $embedCode = "<div class='instagram-post'>
                              <style>
                                .caption {
                                    display: none !important; /* Hide captions */
                                }
                              </style>
                              <blockquote class='instagram-media' data-instgrm-permalink='$instagramPostURL' data-instgrm-version='13'></blockquote>
                              <script async src='//www.instagram.com/embed.js'></script>
                          </div>";

            // Display the embedded Instagram post
            echo $embedCode;
        }
        echo "</div>"; // Close the container
    } else {
        echo "<p>No Instagram posts found in the database.</p>";
    }
    $result->free(); // Free the result set
} else {
    // Display an error message for the SQL statement
    echo "Error: " . $mysqli->error;
}

// Close the database connection
$mysqli->close();
?>
