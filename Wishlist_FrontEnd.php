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
    <title>Wishlist</title>
</head>

<body id="wishlistBody">
    <?php include 'Header.php'; ?>
    <div id="hiddenfortop">
    <div id="wishlistTrips-fe"> 
        <div class="title-bar">
            <h1 id="wishlistTripsHeading"><a href="Wishlist_FrontEnd.php" style="text-decoration: none; color: black; font-size: 24px;">Wishlist</a></h1>
        </div>

        <?php
        include 'UDB.php';

        $userId = $userDetails["id"];

        $wishlistQuery = "SELECT w.*, t.tripTitle, t.tripDate, t.tripPrice FROM wishlists AS w
                        INNER JOIN trips AS t ON w.tripId = t.tripId
                        WHERE w.userId = $userId
                        ORDER BY t.tripDate DESC";

        $wishlistResult = $mysqli->query($wishlistQuery);

        if (!$wishlistResult) {
            die("Query failed: " . $mysqli->error);
        }

        // Check if there are wishlist items to display
        if ($wishlistResult->num_rows > 0) {
            echo '<div class="wishlist-contain-fe clearfix">';

            while ($row = $wishlistResult->fetch_assoc()) {
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

                echo '<div class="wishlist-container-fe" style="background-color: white; margin-right: 20px; padding: 15px 15px; border-radius: 6px; align-items: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">';
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
            }

            echo '</div>';
        } else {
            echo '<p style="padding-left: 130px;">No items in your wishlist.</p>';
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

