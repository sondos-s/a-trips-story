<?php
// Include your database connection file (UDB.php)
require_once 'UDB.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the updated bitInfo from the form
    $updatedBitInfo = $_POST['bitInfo'];

    // Update the bitInfo in the database for bitId = 1
    $sql = "UPDATE bitpayment SET bitInfo = ? WHERE bitId = 1";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $updatedBitInfo);
        if ($stmt->execute()) {
            // Successfully updated bitInfo
            $alertMessage = "BitInfo updated successfully!";
        } else {
            // Display an error message for the SQL statement execution
            $alertMessage = "Error updating BitInfo: " . $mysqli->error;
        }
        $stmt->close();
    } else {
        // Display an error message for the SQL statement preparation
        $alertMessage = "Error preparing the update statement: " . $mysqli->error;
    }
}

// Retrieve the current bitInfo value from the database
$query = "SELECT bitInfo FROM bitpayment WHERE bitId = 1";
$result = $mysqli->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    if ($row && isset($row["bitInfo"])) {
        $currentBitInfo = $row["bitInfo"];
    } else {
        // Handle the case where "bitInfo" is not found in the result
        $alertMessage = "Error: 'bitInfo' not found in the database result.";
    }
} else {
    // Display an error message for retrieving the current bitInfo
    $alertMessage = "Error retrieving BitInfo from the database: " . $mysqli->error;
}
?>
    <!-- Display the alert message using JavaScript -->
    <script>
        window.onload = function() {
            var alertMessage = <?php echo json_encode($alertMessage); ?>;
            if (alertMessage !== "") {
                alert(alertMessage);
            }
        };
    </script>
