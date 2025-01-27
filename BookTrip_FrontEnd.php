<?php
include 'BookTrip_FetchData_BackEnd.php';
?>
<?php include 'BitInfo_Update_BackEnd.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>
        <?php echo isset($tripData["tripTitle"])
            ? htmlspecialchars($tripData["tripTitle"])
            : "Trip Details"; ?>
    </title>
</head>

<body>
    <?php include "Header.php"; ?>

    <div id="containbook" style="background-color: white; height: 900px; margin-top: 1450px;">
        <button class="copyurlbutton" onclick="copyUrl(); playAudio();">&nbsp;<i class="fa fa-share-alt-square fa-3x"
                aria-hidden="true"></i></button>
        <?php
        // Include the existing database connection from UDB.php
        $mysqli = require __DIR__ . "/UDB.php";

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
                $locationQuery =
                    "SELECT locationName FROM locations WHERE id = ?";
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

                echo "<div id='content1' class='content'>";

                echo "<h2 class='triptitle' style=\'font-family: Candara, Calibri, Segoe, Segoe UI, Optima, Arial, sans-serif;\'><span>{$tripData["tripTitle"]}</span></h2>";
                echo "<div class='rowtrip' style='background-color: white;'>";
                echo "<br>";

                echo "<div class='columntrip' id='leftboxtrip'>";
                echo "<i class='fa fa-map-marker fa-lg circle-icon-location' style='background-color: #c76377; color: #4f0f1c;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$locationName}</p>";
                echo "</div>";

                echo "<div class='columntrip' id='middleboxtrip'>";
                echo "<i class='fa fa-calendar fa-lg circle-icon-date' style='background-color: #77a0a3; color: #2c4a4d;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$tripData["tripDate"]}</p>";
                echo "</div>";

                echo "<div class='columntrip' id='middleboxtrip2'>";
                echo "<i class='fa fa-clock-o fa-lg circle-icon-time' style='background-color: #edb677; color: #6e4619;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$tripData["tripTime"]}</p>";
                echo "</div>";

                echo "<div class='columntrip' id='middleboxtrip3'>";
                echo "<i class='fa fa-ils fa-lg circle-icon-price' style='background-color: #9bbf6b; color: #496821;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$tripData["tripPrice"]}</p>";
                echo "</div>";

                echo "<div class='columntrip' id='rightboxtrip'>";
                echo "<i class='fa fa-users fa-lg circle-icon-maxparti' style='background-color: #7d536c; color: #532541;'></i>";
                echo "<p style=\"font-family: 'Jost', sans-serif; font-size: 16px;\">{$tripData["maxParticipants"]}</p>";
                echo "</div>";

                echo '<hr class="section-divider">';

                echo '<form method="POST">        
                        <div class="paymentinputs">
                            <div class ="payment-selector">
                            <h3 style="float: left;">Booking Details</h3>
                            <br><br><br>
                                <p>&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;
                                Participants Number:&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="int" id="participants" name="pNum" required value="' . (isset($bookingDetails['participants']) ? htmlspecialchars($bookingDetails['participants']) : '') . '">
                                </p>
                            </div>
                            <br><br><br><br><br><br>
                            <div class="payment-selector">
                                <div class="payment-options" style="font-family: Candara, Calibri, Segoe, Segoe UI, Optima, Arial, sans-serif;">
                                    <p>&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;Payment Method:</p>
                                </div>
                                <div class="options-section">
                                    <div class="option">
                                        <input type="radio" id="cash" name="payment-method" value="cash" ' . ((isset($bookingDetails['paymentMethod']) && $bookingDetails['paymentMethod'] === 'cash') ? 'checked' : '') . '>
                                        <label for="cash" style="align-items: center;">
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                            Cash<br><br><br><br>
                                            <img src="ViewStyles/PaymentMethods/cash.png" style="height: 30px; width: 50px;">
                                        </label>
                                    </div>
                                    <div class="option">
                                        <input type="radio" id="bitcoin" name="payment-method" value="bit" ' . ((isset($bookingDetails['paymentMethod']) && $bookingDetails['paymentMethod'] === 'bit') ? 'checked' : '') . '>
                                        <label for="bitcoin" style="align-items: center;">
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                            Bit<br><br><br><br>
                                            <img src="ViewStyles/PaymentMethods/bit.png" style="height: 50px; width: 50px;">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr class="section-divider">
                            <br>
                            <div class="selection-container">
                                <p class="selected-price">
                                    Total Price: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span id="total-price"></span>
                                </p>
                                <br>
                                <p class="selected-method">
                                    Selected method: 
                                    <span id="selected-method">' . (isset($bookingDetails['paymentMethod']) ? htmlspecialchars($bookingDetails['paymentMethod']) : '') . '</span>
                                </p>
                            </div>
                        </div>
                        <br><br>
                    </form>';
            echo '<div class="button-container">
            <br>
            <button class="gobackbutton" onclick="goBack()">
                <i class="fa fa-chevron-circle-left"></i>&nbsp;&nbsp;Back
            </button>
            <button class="checkoutbutton" onclick="checkout()">
                <i class="fa fa-check-circle"></i>&nbsp;&nbsp;Checkout
            </button>
            </div>';
            } else {
                echo "<p>No trip found with the given ID.</p>";
            }
        } else {
            echo "<p>Trip ID not provided.</p>";
        }
        ?>
    </div>
    </div>
    <div class="unvisiblediv" style="margin-top: 100px;">
        <h2>&nbsp;&nbsp;&nbsp;</h2>
    </div>
    <div id="bitPopup" class="popup">
        <div class="popup-content">
            <a class="popup-close" onclick="closeBitPopup()">×</a>
            <h3 class="popup-title">Bit Payment</h3>
            <p class="popup-text" style="float: left;">You can transfer us the money using the following phone number in Bit application!</p>
            <hr class="section-divider">
            <p class="popup-text" style="float: left;">Phone Number: </p>
            <br>
            <p class="popup-text" style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($currentBitInfo); ?></p>
            <br><br><br>
            <br><br>
            <button class="donebutton" onclick="confirmBitPayment()" style="align-items: center;">
                <i class="fa fa-check"></i>&nbsp;&nbsp;DONE
            </button>
        </div>
    </div>
    <div id="cashPopup" class="popup">
        <div class="popup-content">
            <a class="popup-close" onclick="closeCashPopup()">×</a>
            <h3 class="popup-title">Cash Payment</h3>
            <p class="popup-text" style="float: left;">To guarantee your booking, pay part of the amount, you can transfer us the money using the following phone number in Bit application!</p>
            <hr class="section-divider">
            <p class="popup-text" style="float: left;">Phone Number: </p>
            <br>
            <p class="popup-text" style="float: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($currentBitInfo); ?></p>
            <br><br><br>
            <br><br>
            <button class="donebutton" onclick="confirmCashPayment()" style="align-items: center;">
                <i class="fa fa-check"></i>&nbsp;&nbsp;DONE
            </button>
        </div>
    </div>
</body>

</html>

<!-- Copy URL Button -->
<script>
    function copyUrl() {
        var url = window.location.href; // get the current URL
        navigator.clipboard.writeText(url); // copy the URL to the clipboard
        alert("URL copied to clipboard!");
    }
</script>

<!-- Total Price Calculation -->
<script>
    // Fetch the trip price from PHP and ensure it's a valid number
    var pricePerParticipant = <?php echo isset($tripData["tripPrice"]) ? floatval($tripData["tripPrice"]) : 0.0; ?>;

    // Function to update the total price based on participants input
    function updateTotalPrice() {
        var participantsInput = document.getElementById("participants");
        var totalParticipants = parseInt(participantsInput.value);

        // Check if price and participant count are valid
        if (!isNaN(pricePerParticipant) && !isNaN(totalParticipants) && totalParticipants > 0) {
            var totalPrice = pricePerParticipant * totalParticipants;
            var totalPriceElement = document.getElementById("total-price");
            totalPriceElement.textContent = totalPrice.toFixed(2) + " ₪";
        } else {
            var totalPriceElement = document.getElementById("total-price");
            totalPriceElement.textContent = "0.00 ₪"; // Show zero if inputs are not valid
        }
    }

    // Attach the updateTotalPrice function to the input event
    var participantsInput = document.getElementById("participants");
    participantsInput.addEventListener("input", updateTotalPrice);

    // Call the updateTotalPrice function initially to show the default value
    updateTotalPrice();
</script>

<script>
    const options = document.querySelectorAll(".option input[type=\'radio\']");
    const selectedMethod = document.querySelector("#selected-method");
    console.log("Booking Details Payment Method: ", '<?php echo isset($bookingDetails['paymentMethod']) ? $bookingDetails['paymentMethod'] : ''; ?>');

    options.forEach((option) => {
        option.nextElementSibling.addEventListener("click", () => {
            selectedMethod.textContent = option.nextElementSibling.textContent;
        });
    });

</script>

<script>
    function goBack() {
        window.history.back(); // Navigate back to the previous page
    }
</script>

<script>
    const bitOption = document.querySelector("#bitcoin");
    const bitPopup = document.querySelector("#bitPopup");

    bitOption.addEventListener("change", toggleBitPopup);

    function toggleBitPopup() {
        if (bitOption.checked) {
            bitPopup.style.display = "block";
        } else {
            bitPopup.style.display = "none";
        }
    }

    function closeBitPopup() {
        bitPopup.style.display = "none";
        bitOption.checked = false; // Unselect the "Bit" option
        selectedMethod.textContent = null;
    }

    function confirmBitPayment() {
        bitPopup.style.display = "none";
        // Keep the "Bit" option selected
        bitOption.checked = true;
    }
</script>

<script>
    function checkout() {
        
    var isTripBooked = false; // Set this variable based on your logic
    var participants = document.getElementById("participants").value;
    var paymentMethod = document.querySelector(".option input[type='radio']:checked");

    if (!participants || participants <= 0) {
        alert('Please enter a valid number of participants.');
        return;
    }

    if (!paymentMethod) {
        alert('Please select a payment method.');
        return;
    }

    var confirmationMessage = isTripBooked
        ? "You already booked that trip. Are you sure you want to update the booking information?"
        : "Are you sure you want to checkout?";

    var result = confirm(confirmationMessage);
    if (result) {
        var total_price = (parseFloat(participants) * pricePerParticipant).toFixed(2);
        var tripId = <?php echo $tripId; ?>;

        // Send data to the backend for processing
        fetch('BookTrip_Update_BackEnd.php', {
            method: 'POST',
            body: new URLSearchParams({
                trip_id: tripId,
                participants: participants,
                total_price: total_price,
                payment_method: paymentMethod.value
            })
        })
        .then(response => response.text())
        .then(result => {
            alert(result);
            // Check if the result is a success message
            if (result.startsWith("Booking updated successfully.")) {
                // Redirect to BookingSummary.php
                var userId = <?php echo $userId; ?>; // Make sure you have the userId available in your JavaScript
                window.location.href = 'BookingSummary_BackEnd.php' +
                    '?trip_id=<?php echo $tripId; ?>' +
                    '&participants=' + participants +
                    '&paymentMethod=' + paymentMethod.value +
                    '&user_id=' + userId;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>


<script>
    const cashOption = document.querySelector("#cash");
    const cashPopup = document.querySelector("#cashPopup");

    cashOption.addEventListener("change", toggleCashPopup);

    function toggleCashPopup() {
        if (cashOption.checked) {
            cashPopup.style.display = "block";
        } else {
            cashPopup.style.display = "none";
        }
    }

    function closeCashPopup() {
    const cashPopup = document.querySelector("#cashPopup");
    cashPopup.style.display = "none";
    const cashOption = document.querySelector("#cash");
    if (cashOption) {
        cashOption.checked = false; // Unselect the "Cash" option if it exists
    }
    const selectedMethod = document.querySelector("#selected-method");
    if (selectedMethod) {
        selectedMethod.textContent = null;
    }
}

    function confirmCashPayment() {
        cashPopup.style.display = "none";
        // Keep the "Cash" option selected
        cashOption.checked = true;
    }
</script>
