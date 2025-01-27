<?php include 'ProfileEdit_BackEnd.php' ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Profile</title>
    <style>
    .header-message {
        color: green; 
        text-align: center;
        padding: 10px;
        font-size: 18px;
        font-weight: bold;
        font-family: 'Courier New', 'Courier', monospace;
    }
    </style>
</head>
<body id="profilebody">
    <?php include "Header.php"; ?>
    <div id="editProfile">
    <div class="title-bar">
        <h1>Profile</h1>
        <span id="editButton" onclick="toggleEdit()">&#9998;</span>
    </div>
        <?php if (isset($error)) {
            echo "<p>Error: $error</p>";
        } ?>

<?php
    if(isset($_SESSION['message'])) {
        echo "<div class='header-message'>$_SESSION[message]</div>";
        unset($_SESSION['message']); // So it doesn’t get displayed again on page refresh
    }
?>

        <form method="POST" action="">
            <div class="profile-form-group">
                <label for="firstName" class="profile-label">First Name:</label><br>
                <input type="text" name="firstName" value="<?php echo $userDetails[
                    "firstName"
                ]; ?>" readonly disabled><br>
            </div>
            <div class="profile-form-group">
                <label for="lastName" class="profile-label">Last Name:</label><br>
                <input type="text" name="lastName" value="<?php echo $userDetails[
                    "lastName"
                ]; ?>" readonly disabled><br>
            </div>
            <div class="profile-form-group">
                <label for="email" class="profile-label">Email:</label><br>
                <input type="email" name="emailAddress" value="<?php echo $userDetails[
                    "emailAddress"
                ]; ?>"
                    readonly disabled><br>
            </div>
            <div class="profile-form-group">
                <label for="birthdate" class="profile-label">Birth Date:</label><br>
                <input type="date" name="birthDate" value="<?php echo $userDetails[
                    "birthDate"
                ]; ?>" readonly disabled><br>
            </div>
            <div class="profile-form-group">
                <label for="phoneNumber" class="profile-label">Phone Number:</label><br>
                <input type="text" name="phoneNumber" value="<?php echo $userDetails[
                    "phoneNumber"
                ]; ?>" readonly disabled><br>
            </div>
            <div class="profile-form-group">
                <label for="city" class="profile-label">City:</label><br>
                <select name="city" id="city" disabled>
                    <!-- Populate the options with city data -->
            <?php
            $cityQuery = "SELECT id, cityName FROM cities";
            $cityResult = $mysqli->query($cityQuery);

            while ($cityRow = $cityResult->fetch_assoc()) {
                $cityId = $cityRow["id"];
                $cityName = $cityRow["cityName"];
                $selected = $cityId == $userDetails["city"] ? "selected" : "";

                echo "<option value='$cityId' $selected>$cityName</option>";
            }
            ?>
                </select><br>
            </div>

            <div class="profile-form-group">
                <label for="username" class="profile-label">Username:</label><br>
                <input type="text" name="username" value="<?php echo $userDetails[
                    "username"
                ]; ?>" readonly disabled><br>
            </div>

            <div class="profile-form-group">
                <label for="password" class="profile-label">Password:</label><br>
                    <div class="password-input-container">
                        <a href="change_password.php" style="font-size: 14px;">Change Password</a>
                    </div><br>
            </div>

            <div class="profile-form-group">
                <label for="enableNotification" class="profile-label">Enable Notification:</label><br>
                <input type="checkbox" name="enableNotification" id="enableNotification" style="height: 18px;" <?php echo $userDetails["enableNotification"] ? "checked" : ""; ?> readonly disabled><br>
            </div>
            
            <input type="submit" value="Save" id="saveButton" class="profile-save-changes-button" style="display: none" disabled onclick="confirmSaveChanges()">
            <br><br><br><br>
            <div id="bookedTrips">
                <div class="title-bar">
                    <h1 id="bookedTripsHeading"><a href="BookedTrips_FrontEnd.php" style="text-decoration: none; color: black;">Booked Trips</a></h1>
                </div>
                    <div id="bookedTripsList">
                        <div class="container">
                            <div class="table-wrapper">
                                <div class="table-container">
                                    <table>
                                        <tr class="table-row">
                                            <th>Trip Title</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Participants</th>
                                            <th>Total Price</th>
                                            <th>Payment Method</th>
                                            <th>Update/Cancel Booking</th>
                                        </tr>
                                    <?php
                                    $limit = 3; // Number of rows to display initially
                                    $count = 0;

                                    // Fetch the user's booked trips
                                    $userId = $userDetails["id"];
                                    $query = "SELECT b.*, t.tripTitle, t.tripDate, t.tripTime FROM bookings AS b
                                            INNER JOIN trips AS t ON b.tripId = t.tripId
                                            WHERE b.userId = $userId
                                            ORDER BY t.tripDate DESC";

                                    $result = $mysqli->query($query);

                                    // Get the current date in the same format as $row['tripDate']
                                    $currentDate = date("Y-m-d");

                                    // Initialize a variable to count booked trips
                                    $bookedTripsCount = 0;

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {

                                            // Display only the first 3 rows
                                            if ($count < $limit) {
                                                // Get the trip title and id
                                                $tripTitle = $row['tripTitle'];
                                                $tripId = $row['tripId'];

                                                // Make an API call to Wikipedia to fetch the trip image
                                                $tripTitleEncoded = urlencode(str_replace(' ', '_', $tripTitle));
                                                $apiUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/{$tripTitleEncoded}";
                                                $apiResponse = file_get_contents($apiUrl);
                                                $apiData = json_decode($apiResponse, true);
                                                $imageUrl = isset($apiData['thumbnail']['source']) ? $apiData['thumbnail']['source'] : '';

                                                // Check if the trip date is in the past
                                                $isPastDate = $row['tripDate'] < $currentDate;
                                                  /// cancel more than two days before trip date
                                    // Calculate the difference in days between the trip date and current date
                                    $tripDate = strtotime($row['tripDate']);
                                    $daysUntilTrip = floor(($tripDate - strtotime(date('Y-m-d'))) / (60 * 60 * 24));

                                    // Check if the trip date is in two days or less
                                    $isTwoDaysOrLess = $daysUntilTrip <= 2;

                                                // Output each booked trip as a table row
                                                echo '<tr class="table-row">';
                                                echo '<td><a href="TripDetails_FrontEnd2.php?trip_id=' . $tripId . '&tripTitle=' . urlencode($tripTitle) . '" style="text-decoration: underline; color: black; font-weight: bold;">';
                                                echo '<div class="square-image-container"><img src="' . $imageUrl . '" alt="Trip Image" style="width: 80px; height: 80px; overflow: hidden; border-radius: 2px;"></div>';
                                                echo '<div>' . $tripTitle . '</div></a></td>';
                                                echo '<td>' . $row['tripDate'] . '</td>'; // Display the trip date
                                                echo '<td>' . $row['tripTime'] . '</td>'; // Display the trip time
                                                echo '<td>' . $row['participants'] . '</td>'; // Display the number of participants
                                                echo '<td>' . $row['totalPrice'] . '</td>'; // Display the total price
                                                echo '<td>' . $row['paymentMethod'] . '</td>'; // Display the payment method
                                                
                                                // Check if the trip date is in the past and apply styling accordingly
                                                if ($isPastDate|| $isTwoDaysOrLess) {
                                                    echo '<td>';
                                                    echo '<a style="color: grey;" class="update-link" disabled><i class="fa fa-pencil-square-o"></i> Update</a>';
                                                    echo '<a style="color: grey;" class="cancel-link" disabled><i class="fa fa-times"></i> Cancel</a>';
                                                    echo '</td>';
                                                } else {
                                                    // Display the "Update" and "Cancel" links
                                                    echo '<td>';
                                                    echo '<a href="BookTrip_FrontEnd.php?booking_id=' . $row['bookingId'] . '&trip_id=' . $row['tripId'] . '" class="update-link"><i class="fa fa-pencil-square-o"></i> Update</a>';
                                                    echo '<a href="BookTrip_Delete_BackEnd.php?booking_id=' . $row['bookingId'] . '" class="cancel-link" onclick="confirmCancelBooking()"><i class="fa fa-times"></i> Cancel</a>';
                                                    echo '</td>';
                                                }

                                                echo '</tr>';
                                                $count++;
                                                $bookedTripsCount++; // Increment the count for booked trips
                                            } else {
                                                            // If there are more rows, break out of the loop
                                                            break;
                                                        }
                                                    }
                                            } else {
                                                echo '<tr><td colspan="7">No booked trips found.</td></tr>';
                                            }
                                            ?>
                                    <tr class="show-more-row">
                                        <td colspan="7">
                                            <?php
                                            // Check if there are more than 2 booked trips to show the "Show All Booked Trips" row
                                            if ($bookedTripsCount > 2) {
                                                echo '<a href="BookedTrips_FrontEnd.php" class="show-more-button">';
                                                echo '<i class="fa fa-chevron-down"></i> Show All Booked Trips';
                                                echo '</a>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="wishlistTrips">
                <div class="title-bar">
                    <h1 id="wishlistTripsHeading"><a href="Wishlist_FrontEnd.php" style="text-decoration: none; color: black;">Wishlist </a></h1>
                </div>
                <?php
                include 'UDB.php';

                $userId = $userDetails["id"]; // Replace with the actual user ID

                $wishlistQuery = "SELECT w.*, t.tripTitle, t.tripDate, t.tripPrice FROM wishlists AS w
                                INNER JOIN trips AS t ON w.tripId = t.tripId
                                WHERE w.userId = $userId
                                ORDER BY t.tripDate DESC";

                $wishlistResult = $mysqli->query($wishlistQuery);

                if (!$wishlistResult) {
                    die("Query failed: " . $mysqli->error);
                }

                // Initialize a variable to count wishlist items
                $wishlistCount = 0;
                $limit = 4; // Limit the number of displayed wishlist items

                // Check if there are wishlist items to display
                if ($wishlistResult->num_rows > 0) {
                    echo '<div class="wishlist-contain">';

                    while ($row = $wishlistResult->fetch_assoc()) {
                        // Check if the limit has been reached
                        if ($wishlistCount >= $limit) {
                            break;
                        }
                        
                        $tripTitle = $row['tripTitle'];
                        $tripDate = $row['tripDate'];
                        $tripPrice = $row['tripPrice'];
                        $tripId = $row['tripId'];

                        // Make an API call to Wikipedia to fetch the trip image
                        $tripTitleEncoded = urlencode(str_replace(' ', '_', $tripTitle));
                        $apiUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/{$tripTitleEncoded}";
                        $apiResponse = file_get_contents($apiUrl);
                        $apiData = json_decode($apiResponse, true);
                        $imageUrl = isset($apiData['thumbnail']['source']) ? $apiData['thumbnail']['source'] : '';

                        echo '<div class="wishlist-container" style="background-color: white; margin-right: 20px; padding: 15px 15px; border-radius: 6px; align-items: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">';
                        echo '<a style="text-decoration: none;" href="TripDetails_FrontEnd2.php?trip_id=' . $tripId . '&tripTitle=' . urlencode($tripTitle) . '">';
                        echo '<img src="' . $imageUrl . '" alt="Trip Image" style="width: 100px; height: 100px; border-radius: 2px;">';
                        echo '<p class="trip-title" style="font-size: 12px;"><strong>' . $tripTitle . '</strong></p>';
                        echo '<p style="font-size: 10px; text-decoration: none;">Date: ' . $tripDate . '</p>';
                        echo '<p style="font-size: 10px; text-decoration: none;">Price: ' . $tripPrice . '</p>';
                        echo '</a>';
                        echo "<a href='javascript:void(0);' onclick='toggleWishlist({$tripId})'>";
                        echo "<i class='fa fa-heart wishlist-icon' data-trip-id='{$tripId}' style='float: right; color: pink; font-size: 1.5em;'></i>";
                        echo "</a>";
                        echo '</div>';

                        // Increment the wishlist count for each item
                        $wishlistCount++;
                    }
                    
                    echo '</div>';
                } else {
                    echo '<p style="padding-left: 130px;">No items in your wishlist.</p>';
                }
                // Check if there are more wishlist items to show the "Show More" link
                if ($wishlistResult->num_rows > $limit) {
                    echo '<div class="show-more-row">';
                    echo '<a href="Wishlist_FrontEnd.php" class="show-more-button" style="font-size: 12px; align-items: center;">';
                    echo '<i class="fa fa-chevron-down"></i> Show More';
                    echo '</a>';
                    echo '</div>';
                }
                ?>
            </div>
            <div id="waitinglistTrips">
                <div class="title-bar">
                    <h1 id="waitinglistTripsHeading"><a href="Waitinglist_FrontEnd.php" style="text-decoration: none; color: black;">Waitinglist </a></h1>
                </div>
                <?php
                include 'UDB.php';

                $userId = $userDetails["id"];

                $waitinglistQuery = "SELECT wl.*, t.tripTitle, t.tripDate, t.tripPrice FROM waitinglists AS wl
                                INNER JOIN trips AS t ON wl.tripId = t.tripId
                                WHERE wl.userId = $userId
                                AND t.tripDate >= CURDATE()
                                ORDER BY t.tripDate DESC"; // This condition filters out past dates and order from recently

                $waitinglistResult = $mysqli->query($waitinglistQuery);

                if (!$waitinglistResult) {
                    die("Query failed: " . $mysqli->error);
                }

                // Initialize a variable to count waitinglist items
                $waitinglistCount = 0;
                $limitWaitinglist = 4; // Limit the number of displayed waitinglist items

                // Check if there are waitinglist items to display
                if ($waitinglistResult->num_rows > 0) {
                    echo '<div class="waitinglist-contain">';
                    
                    while ($row = $waitinglistResult->fetch_assoc()) {
                        // Check if the limit has been reached
                        if ($waitinglistCount >= $limitWaitinglist) {
                            break;
                        }

                        $tripTitle = $row['tripTitle'];
                        $tripDate = $row['tripDate'];
                        $tripPrice = $row['tripPrice'];
                        $tripId = $row['tripId'];

                        // Make an API call to Wikipedia to fetch the trip image
                        $tripTitleEncoded = urlencode(str_replace(' ', '_', $tripTitle));
                        $apiUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/{$tripTitleEncoded}";
                        $apiResponse = file_get_contents($apiUrl);
                        $apiData = json_decode($apiResponse, true);
                        $imageUrl = isset($apiData['thumbnail']['source']) ? $apiData['thumbnail']['source'] : '';

                        echo '<div class="waitinglist-container" style="background-color: white; margin-right: 20px; padding: 15px 15px; border-radius: 6px; align-items: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">';
                        echo '<a style="text-decoration: none;" href="TripDetails_FrontEnd2.php?trip_id=' . $tripId . '&tripTitle=' . urlencode($tripTitle) . '">';
                        echo '<img src="' . $imageUrl . '" alt="Trip Image" style="width: 100px; height: 100px; border-radius: 2px;">';
                        echo '<p class="trip-title" style="font-size: 14px;"><strong>' . $tripTitle . '</strong></p>';
                        echo '<p style="font-size: 10px; text-decoration: none;">Date: ' . $tripDate . '</p>';
                        echo '<p style="font-size: 10px; text-decoration: none;">Price: ' . $tripPrice . '</p>';
                        echo '</a>';
                        echo "<a href='javascript:void(0);' onclick='toggleWaitinglist({$tripId})'>";
                        echo "<i class='fa fa-hourglass waiting-icon' data-trip-id='{$tripId}' style='float: right; color: lime; font-size: 1.35em;'></i>";
                        echo "</a>";
                        echo '</div>';

                        // Increment the wishlist count for each item
                        $waitinglistCount++;
                    }
                    
                    echo '</div>';
                } else {
                    echo '<p style="padding-left: 130px;">No items in your waitinglist.</p>';
                }
                // Check if there are more waitinglist items to show the "Show More" link
                if ($waitinglistResult->num_rows > $limit) {
                    echo '<div class="show-more-row">';
                    echo '<a href="Waitinglist_FrontEnd.php" class="show-more-button" style="font-size: 12px; align-items: center;">';
                    echo '<i class="fa fa-chevron-down"></i> Show More';
                    echo '</a>';
                    echo '</div>';
                }
                ?>
            </div>
        </form>
    </div>
</body>
</html>

<script>
        function togglePasswordVisibility() {
            const passwordInput = document.querySelector('#password');
            const passwordIcon = document.querySelector('#passwordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        function toggleEdit() {
            const inputFields = document.querySelectorAll('#editProfile input:not([type="submit"])');
            const selectField = document.querySelector('#city'); // Select the city dropdown
            const saveButton = document.querySelector('#saveButton');

            // Toggle readOnly and background color for each input field
            inputFields.forEach(field => {
                field.readOnly = !field.readOnly;
                field.style.backgroundColor = field.readOnly ? 'rgba(255, 255, 255, 0.07)' : 'white';
                field.disabled = !field.disabled; // Toggle disabled attribute
            });

            // Toggle the "Save" button's visibility and disabled state
            saveButton.style.display = saveButton.style.display === 'none' ? 'block' : 'none';
            saveButton.disabled = !saveButton.disabled;

            // Toggle the disabled attribute of the select element
            selectField.style.backgroundColor = selectField.readOnly ? 'rgba(255, 255, 255, 0.07)' : 'white';
            selectField.disabled = !selectField.disabled;
        }

</script>

<script>
    // Set user ID in session (assuming you have a way to authenticate users)
    <?php
    if (isset($_SESSION['user_id'])) {
        echo "const userId = " . $_SESSION['user_id'] . ";";
    } else {
        echo "const userId = null;";
    }
    ?>

    // Wishlist Toggle
    let wishlistClicked = sessionStorage.getItem('wishlistClicked') === 'true'; // Retrieve state from session storage

    // JavaScript code
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize wishlist icon states for all trips
        const wishlistIcons = document.querySelectorAll('.wishlist-icon');
        wishlistIcons.forEach(icon => {
            const tripId = icon.getAttribute('data-trip-id');
            const isWishlistClicked = sessionStorage.getItem(`wishlistClicked_${tripId}`) === 'true';
            if (isWishlistClicked) {
                icon.classList.add('clicked');
                icon.style.color = 'pink';
            }
        });
    });

    function toggleWishlist(tripId) {
        const heartIcon = document.querySelector(`.wishlist-icon[data-trip-id="${tripId}"]`);
        if (userId !== null) {
            const isWishlistClicked = sessionStorage.getItem(`wishlistClicked_${tripId}`) === 'true';
            if (isWishlistClicked) {
                heartIcon.classList.remove('clicked');
                heartIcon.style.color = 'rgb(190, 190, 190)';
                removeFromWishlist(tripId);
            } else {
                heartIcon.classList.add('clicked');
                heartIcon.style.color = 'pink';
                addToWishlist(tripId);
            }
            sessionStorage.setItem(`wishlistClicked_${tripId}`, !isWishlistClicked); // Store state in session storage
        } else {
            // Handle when the user is not logged in
            // You can show an alert or redirect to a login page
        }
    }

    // Add to Wishlist
    function addToWishlist(tripId) {
        const formData = new FormData();
        formData.append('trip_id', tripId);
        fetch('Wishlist_Add_BackEnd.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
    }

    // Remove from Wishlist
    function removeFromWishlist(tripId) {
        const formData = new FormData();
        formData.append('trip_id', tripId);
        fetch('Wishlist_Remove_BackEnd.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
    }


    // Waitinglist Toggle
    let waitinglistClicked = sessionStorage.getItem('waitinglistClicked') === 'true'; // Retrieve state from session storage

    // JavaScript code
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize waitinglist icon states for all trips
        const waitinglistIcons = document.querySelectorAll('.waiting-icon');
        waitinglistIcons.forEach(icon => {
            const tripId = icon.getAttribute('data-trip-id');
            const isWaitinglistClicked = sessionStorage.getItem(`waitinglistClicked_${tripId}`) === 'true';
            if (isWaitinglistClicked) {
                icon.classList.add('clicked');
                icon.style.color = '#9bbf6b';
            }
        });
    });

    function toggleWaitinglist(tripId) {
        const hourGlassIcon = document.querySelector(`.waiting-icon[data-trip-id="${tripId}"]`);
        if (userId !== null) {
            const isWaitinglistClicked = sessionStorage.getItem(`waitinglistClicked_${tripId}`) === 'true';
            if (isWaitinglistClicked) {
                hourGlassIcon.classList.remove('clicked');
                hourGlassIcon.style.color = 'rgb(190, 190, 190)';
                removeFromWaitinglist(tripId);
            } else {
                hourGlassIcon.classList.add('clicked');
                hourGlassIcon.style.color = '#9bbf6b';
                addToWaitinglist(tripId);
            }
            sessionStorage.setItem(`waitinglistClicked_${tripId}`, !isWaitinglistClicked); // Store state in session storage
        } else {
            // Handle when the user is not logged in
            // You can show an alert or redirect to a login page
        }
    }

    // Add to Waitinglist
    function addToWaitinglist(tripId) {
        const formData = new FormData();
        formData.append('trip_id', tripId);
        fetch('WaitingList_Add_BackEnd.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
    }

    // Remove from Waitinglist
    function removeFromWaitinglist(tripId) {
        const formData = new FormData();
        formData.append('trip_id', tripId);
        fetch('WaitingList_Remove_BackEnd.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize wishlist icon states for all trips
        const wishlistIcons = document.querySelectorAll('.wishlist-icon');
        wishlistIcons.forEach(icon => {
            const tripId = icon.getAttribute('data-trip-id');
            const isWishlistClicked = sessionStorage.getItem(`wishlistClicked_${tripId}`) === 'true';
            if (isWishlistClicked) {
                icon.classList.add('clicked');
                icon.style.color = 'pink';
            }
        });

        // Fetch the user's wishlist
        fetch('Wishlist_Fetch.php')
            .then(response => response.json())
            .then(wishlist => {
                // Check if the current trip is in the wishlist
                const currentTripId = <?php echo $tripId; ?>;
                if (wishlist.includes(currentTripId)) {
                    // Set the button color to pink
                    const heartIcon = document.querySelector(`.wishlist-icon[data-trip-id="${currentTripId}"]`);
                    heartIcon.style.color = 'pink';
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    // Initialize waiting list icon states for all trips
    const waitingIcons = document.querySelectorAll('.waiting-icon');
    waitingIcons.forEach(icon => {
        const tripId = icon.getAttribute('data-trip-id');
        const isWaitingClicked = sessionStorage.getItem(`waitingClicked_${tripId}`) === 'true';
        if (isWaitingClicked) {
            icon.classList.add('clicked');
            icon.style.color = '#9bbf6b';
        }
    });

    // Fetch the user's waiting list
    fetch('WaitingList_Fetch.php')
        .then(response => response.json())
        .then(waitinglist => {
            // Check if the current trip is in the waiting list
            const currentTripId = <?php echo $tripId; ?>;
            if (waitinglist.includes(currentTripId)) {
                // Set the button color to green
                const hourGlassIcon = document.querySelector(`.waiting-icon[data-trip-id="${currentTripId}"]`);
                hourGlassIcon.style.color = '#9bbf6b';
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>

<script>
    function confirmCancelBooking() {
        if (confirm("Are you sure you want to cancel this booking?")) {
            // User clicked OK, you can proceed with the cancellation
        }  else {
        // User clicked Cancel, prevent the default behavior (closing the dialog)
        event.preventDefault();
        }
    }
</script>

<script>
    function confirmSaveChanges() {
        if (confirm("Are you sure you want to save changes?")) {
            // User clicked OK, you can proceed with saving changes
            document.getElementById("profileForm").submit(); // Assuming your form has an ID "profileForm"
        } else {
            // User clicked Cancel, no action required
        }
    }
</script>
