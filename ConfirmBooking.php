<?php
include 'UDB.php';
require('fpdf/fpdf.php'); // Include the FPDF library

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

function sendEmailWithPDFAttachment($firstName, $lastName, $email, $subject, $message, $pdfFilePath)
{
    $name = $firstName . ' ' . $lastName;
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Your SMTP host
    $mail->SMTPAuth = true;
    $mail->Username = 'atripsstory@gmail.com'; // Your SMTP username
    $mail->Password = 'efalcuznugkbvhgx'; // Your SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption
    $mail->Port = 587; // TCP port to connect to

    $mail->setFrom('atripsstory@gmail.com', 'Balkes Wishahi'); // Replace with your name
    $mail->addAddress($email, $name); // Add recipient email and name

    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = $subject; // Email subject
    $mail->Body = $message; // Email message

    // Attach the PDF file
    $mail->addAttachment($pdfFilePath);

    // Send the email
    if ($mail->send()) {
        return true; // Email sent successfully
    } else {
        return false; // Email sending failed
    }
}

if (isset($_GET['booking_id'])) {
    $bookingId = intval($_GET['booking_id']);

    // Check if the booking exists
    $checkQuery = "SELECT u.username, u.emailAddress, u.phoneNumber, l.locationName, b.userId, t.tripTitle, t.tripDate, t.tripTime, t.tripLocation
    FROM bookings b
    INNER JOIN users u ON b.userId = u.id
    INNER JOIN trips t ON b.tripId = t.tripId
    INNER JOIN locations l ON u.city = l.id
    WHERE b.bookingId = ?";


    $checkStmt = $mysqli->prepare($checkQuery);

    if (!$checkStmt) {
        die("Error in checkQuery: " . mysqli_error($mysqli));
    }

    $checkStmt->bind_param("i", $bookingId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $userId = $row['userId'];
        $userEmail = $row['emailAddress'];
        $tripTitle = $row['tripTitle'];
        $tripDate = $row['tripDate'];
        $tripTime = date('H:i', strtotime($row['tripTime'])); // Format tripTime without seconds


        // Retrieve additional booking details
        $bookingDetailsQuery = "SELECT participants, totalPrice, paymentMethod
    FROM bookings
    WHERE bookingId = ?";
        $bookingDetailsStmt = $mysqli->prepare($bookingDetailsQuery);

        if (!$bookingDetailsStmt) {
            die("Error in bookingDetailsQuery: " . mysqli_error($mysqli));
        }

        $bookingDetailsStmt->bind_param("i", $bookingId);
        $bookingDetailsStmt->execute();
        $bookingDetailsResult = $bookingDetailsStmt->get_result();

        if ($bookingDetailsResult->num_rows === 1) {
            $bookingDetailsRow = $bookingDetailsResult->fetch_assoc();
            $participants = $bookingDetailsRow['participants'];
            $totalPrice = $bookingDetailsRow['totalPrice'];
            $paymentMethod = $bookingDetailsRow['paymentMethod'];
        }
        // Retrieve user's first name and last name
        $userInfoQuery = "SELECT users.*, locations.locationName AS cityName FROM users LEFT JOIN locations ON users.city = locations.id WHERE users.id = ?";
        $userInfoStmt = $mysqli->prepare($userInfoQuery);

        if (!$userInfoStmt) {
            die("Error in userInfoQuery: " . mysqli_error($mysqli));
        }

        $userInfoStmt->bind_param("i", $userId);
        $userInfoStmt->execute();
        $userInfoResult = $userInfoStmt->get_result();

        if ($userInfoResult->num_rows === 1) {
            $userInfoRow = $userInfoResult->fetch_assoc();
            $userFirstName = $userInfoRow['firstName'];
            $userLastName = $userInfoRow['lastName'];
            $userCity = $userInfoRow['cityName']; // This line fetches the city name
        }

        // Fetch the location name based on the tripLocation ID
        $tripLocationId = $row['tripLocation'];
        $locationNameQuery = "SELECT `locationName` FROM `locations` WHERE `id` = ?";
        $locationNameStmt = $mysqli->prepare($locationNameQuery);

        if (!$locationNameStmt) {
            die("Error in locationNameQuery: " . mysqli_error($mysqli));
        }

        $locationNameStmt->bind_param("i", $tripLocationId);
        $locationNameStmt->execute();
        $locationNameResult = $locationNameStmt->get_result();

        if ($locationNameResult->num_rows === 1) {
            $locationNameRow = $locationNameResult->fetch_assoc();
            $tripLocation = $locationNameRow['locationName']; // Update $tripLocation with location name
        }
        // Create a PDF with booking details
        $pdf = new FPDF();
        $pdf->AddPage();
        // Add logo image to the top right corner
        $logoImagePath = 'ViewStyles\Footer\logo.png'; // Specify the path to your logo image
        $pdf->Image($logoImagePath, 160, 10, 30); // Adjust the coordinates and size as needed
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Trip Confirmation', 0, 1, 'C');
        $pdf->Ln(15); // Add space between title and content
        // Add user's first name and last name to the PDF
        $pdf->Cell(40, 10, 'User Name: ' . $userFirstName . ' ' . $userLastName);
        $pdf->Ln(15); // Add space between user's name and other content in the PDF
        $pdf->Cell(40, 10, 'Username: ' . $row['username']);
        $pdf->Ln(15); // Add space between username and other content in the PDF
        $pdf->Cell(40, 10, 'Email: ' . $row['emailAddress']);
        $pdf->Ln(15); // Add space between email and other content in the PDF
        $pdf->Cell(40, 10, 'Phone Number: ' . $row['phoneNumber']);

        $pdf->Ln(15);
        $pdf->Cell(40, 10, 'City: ' . $userCity);
        $pdf->Ln(15); // Add space between city and other content in the PDF

        //////////////////////////////////////////////////
        $pdf->Cell(40, 10, '_____________________________ ');
        $pdf->Ln(15);
        $pdf->Cell(40, 10, 'Trip inforamtion- ');
        $pdf->Ln(15);

        // Add booking details to the PDF (customize as needed)
        $pdf->Cell(40, 10, 'Trip Title: ' . $tripTitle);
        $pdf->Ln(15); // Add space between title and content
        $pdf->Cell(40, 10, 'Trip Date: ' . $tripDate);
        $pdf->Ln(15); // Add space between title and content
        // Add trip time to the PDF
        $pdf->Cell(40, 10, 'Trip Time: ' . $tripTime); // Use the formatted tripTime
        $pdf->Ln(15);
        // fix it
        $pdf->Cell(40, 10, 'Trip Location: ' . $tripLocation);
        $pdf->Ln(15);

        //////////////////////////////////////////////////
        $pdf->Cell(40, 10, '_____________________________ ');
        $pdf->Ln(15);
        $pdf->Cell(40, 10, 'Booking inforamtion- ');
        $pdf->Ln(15);
        $pdf->Cell(40, 10, 'Participants: ' . $participants);
        $pdf->Ln(15); // Add space between title and content
        $pdf->Cell(40, 10, 'Total Price: ' . $totalPrice);
        $pdf->Ln(15); // Add space between title and content
        $pdf->Cell(40, 10, 'Payment Method: ' . $paymentMethod);
        $pdf->Ln(15); // Add space between title and content
        // ...

        // Save the PDF to a temporary file
        $pdfFilePath = 'booking_conformation.pdf';
        $pdf->Output($pdfFilePath, 'F');


        // Perform any necessary confirmation actions here
        // For example, send an email to the user
        $subject = "Your Booking has been Approved";
        $message = "Congratulations! Your booking for \"$tripTitle\" on $tripDate has been approved,<br>you can cancel your booking only of the trip date is in more than 2 days";

        if (sendEmailWithPDFAttachment($userFirstName, $userLastName, $userEmail, $subject, $message, $pdfFilePath)) {
            // Email sent successfully, display a popup message
            header("Location: TripsBookingsManagement_FrontEnd.php");
        } else {
            // Email sending failed
            echo "Error: Unable to send email confirmation with PDF attachment.";
        }

        // Delete the temporary PDF file
        unlink($pdfFilePath);
    } else {
        // Booking ID not provided, redirect to an error page or display an error message
        echo "Error: Booking ID not provided.";
    }
}
