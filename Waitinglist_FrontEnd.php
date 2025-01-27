<?php include 'ProfileEdit_BackEnd.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Waitinglist</title>
</head>

<body id="waitinglistBody">
    <?php include 'Header.php'; ?>
    <div id="hiddenfortop">
    <div id="waitinglistTrips-fe"> 
        <div class="title-bar">
            <h1 id="waitinglistTripsHeading"><a href="Waitinglist_FrontEnd.php" style="text-decoration: none; color: black; font-size: 24px;">Waitinglist</a></h1>
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

        // Check if there are waitinglist items to display
        if ($waitinglistResult->num_rows > 0) {
            echo '<div class="waitinglist-contain-fe clearfix">';

            while ($row = $waitinglistResult->fetch_assoc()) {
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

                echo '<div class="waitinglist-container-fe" style="background-color: white; margin-right: 20px; padding: 15px 15px; border-radius: 6px; align-items: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">';
                echo '<a style="text-decoration: none;" href="TripDetails_FrontEnd2.php?trip_id=' . $tripId . '&tripTitle=' . urlencode($tripTitle) . '">';
                echo '<img src="' . $imageUrl . '" alt="Trip Image" style="width: 100px; height: 100px; border-radius: 2px;">';
                echo '<p class="trip-title" style="font-size: 12px;"><strong>' . $tripTitle . '</strong></p>';
                echo '<p style="font-size: 10px; text-decoration: none;">Date: ' . $tripDate . '</p>';
                echo '<p style="font-size: 10px; text-decoration: none;">Price: ' . $tripPrice . '</p>';
                echo '</a>';
                echo "<a href='javascript:void(0);' onclick='toggleWaitinglist({$tripId})'>";
                echo "<i class='fa fa-hourglass waiting-icon' data-trip-id='{$tripId}' style='float: right; color: lime; font-size: 1.35em;'></i>";
                echo "</a>";
                echo '</div>';

            }
            echo '</div>';
        } else {
            echo '<p style="padding-left: 130px;">No items in your waitinglist.</p>';
        }
        ?>
    </div>
    </div>
</body>

</html>

<script>
    // Set user ID in session (assuming you have a way to authenticate users)
    <?php
    if (isset($_SESSION['user_id'])) {
        echo "const userId = " . $_SESSION['user_id'] . ";";
    } else {
        echo "const userId = null;";
    }
    ?>

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
