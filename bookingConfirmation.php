<?php
require('fpdf/fpdf.php');

// Include the existing database connection from UDB.php
$mysqli = require __DIR__ . "/UDB.php";

// Retrieve data from query parameters
$participants = $_GET['participants'];
$paymentMethod = $_GET['paymentMethod'];
$userId = $_GET['user_id']; // Add this line to retrieve the user ID

// Get the trip ID from the query parameters
if (isset($_GET["trip_id"])) {
    $tripId = $_GET["trip_id"];

    // Prepare and execute the SQL query to retrieve trip details
    $query = "SELECT * FROM trips WHERE tripId = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $tripId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the trip data
        $tripData = $result->fetch_assoc();

        // Fetch location name based on tripLocation ID
        $locationQuery = "SELECT locationName FROM locations WHERE id = ?";
        $locationStmt = $mysqli->prepare($locationQuery);
        $locationStmt->bind_param("i", $tripData["tripLocation"]);
        $locationStmt->execute();
        $locationResult = $locationStmt->get_result();

        if ($locationResult->num_rows > 0) {
            $locationData = $locationResult->fetch_assoc();
            $locationName = $locationData["locationName"];
        } else {
            $locationName = "Unknown Location";
        }

        // Fetch user information based on user ID
        $userQuery = "SELECT users.*, locations.locationName AS cityName FROM users LEFT JOIN locations ON users.city = locations.id WHERE users.id = ?";

        $userStmt = $mysqli->prepare($userQuery);
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();

        if ($userResult->num_rows > 0) {
            $userData = $userResult->fetch_assoc();
            $userName1 = $userData["firstName"] . " " . $userData["lastName"];
            $userEmail = $userData["emailAddress"];
            $userName = $userData["username"];
            $userPhoneNumber = $userData["phoneNumber"];
            $userCity = $userData["cityName"]; // Use cityName instead of city

        } else {
            $userName = "Unknown User";
        }
         // Prepare and execute the SQL query to retrieve booking details including total price
         $bookingQuery = "SELECT `bookingId`, `userId`, `tripId`, `participants`, `totalPrice`, `paymentMethod` FROM `bookings` WHERE 1";
         $bookingResult = $mysqli->query($bookingQuery);
 
         if ($bookingResult->num_rows > 0) {
             $bookingData = $bookingResult->fetch_assoc();
             $totalPrice = $bookingData["totalPrice"]; // Fetch the total price from the booking data
         } else {
             $totalPrice = "Unknown"; // Set a default value if the booking data is not found
         }
 
        // Create a PDF document
        class PDF extends FPDF
        {
            function Header()
            {
                // Logo image path on your server
                $logoPath = 'logo.png';
            
                // Position the logo at the right corner
                $this->Image($logoPath, 170, 10, 30); // Adjust the X, Y, and size as needed
            }
            

            function Footer()
            {
                // Add a footer with page number here if needed
            }
        }

        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Header(); // Call the Header function to add the logo
        $pdf->Cell(0, 10, 'Trip Confirmation', 0, 1, 'C'); 
        $pdf->Ln(15); // Add space between title and content

        // Add user's name and trip details to the PDF
        $pdf->Cell(0, 10, 'Full Name: ' . $userName1); // Display user's full name
        $pdf->Ln(15); 
         // Add user's name and trip details to the PDF
         $pdf->Cell(0, 10, 'UserName: ' . $userName); // Display user's full name
         $pdf->Ln(15); 
          // Add user's name and trip details to the PDF
        $pdf->Cell(0, 10, 'Email address: ' . $userEmail); // Display user's full name
        $pdf->Ln(15); 
         // Add user's name and trip details to the PDF
         $pdf->Cell(0, 10, 'phone number: ' . $userPhoneNumber); // Display user's full name
         $pdf->Ln(15); 
          // Add user's name and trip details to the PDF
        $pdf->Cell(0, 10, 'city: ' . $userCity); // Display user's full name
        $pdf->Ln(15); 
        //////////////////////////////////////////////////
        $pdf->Cell(0, 15, '______________________________________________________________________________');
        $pdf->Cell(0, 15, 'Trip inforamtion ');

        $pdf->Cell(0, 10, 'Title: ' . $tripData["tripTitle"]);
        $pdf->Ln(15); 
        $pdf->Cell(0, 10, 'Location: ' . $locationName);
        $pdf->Ln(15);
        $pdf->Cell(0, 10, 'Date: ' . $tripData["tripDate"]);
        $pdf->Ln(15);
        $pdf->Cell(0, 10, 'Time: ' . $tripData["tripTime"]);
        $pdf->Ln(15);
        $pdf->Cell(0, 15, '______________________________________________________________________________');
        // Add trip details, user details, and booking details based on the retrieved data
        $pdf->Cell(0, 15, 'Booking inforamtion ');

        $pdf->Cell(40, 10, 'Booked Slots Number: ' . $participants);
        $pdf->Ln(15);
        $pdf->Cell(40, 10, 'Payment Method:₪ ' . $paymentMethod);
        $pdf->Ln(15);
        $pdf->Cell(40, 10, 'Payment Method: ' . $totalPrice);

        // Output the PDF
        $pdf->Output();

    } else {
        echo "<p>No trip found with the given ID.</p>";
    }
} else {
    echo "<p>Trip ID not provided.</p>";
}
?>
