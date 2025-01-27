<?php
    include 'UDB.php';

    // Fetch data from your database
    $query = "SELECT c.cityName AS City, COUNT(b.userId) AS ParticipantsCount
            FROM bookings b
            JOIN users u ON b.userId = u.id
            JOIN cities c ON u.city = c.id
            GROUP BY c.cityName";
    $result = mysqli_query($conn, $query);

    // Create arrays to store the city names and participant counts
    $cities = [];
    $participantsCount = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $cities[] = $row['City'];
        $participantsCount[] = $row['ParticipantsCount'];
    }

    // Fetch the data
    $sqlRegisteredUsers = "SELECT COUNT(*) AS totalUsers FROM users";
    $sqlBookedUsers = "SELECT COUNT(DISTINCT userid) AS bookedUsers FROM bookings";

    $resultRegistered = $conn->query($sqlRegisteredUsers);
    $resultBooked = $conn->query($sqlBookedUsers);

    $rowRegistered = $resultRegistered->fetch_assoc();
    $rowBooked = $resultBooked->fetch_assoc();

    $totalUsers = $rowRegistered['totalUsers'];
    $bookedUsers = $rowBooked['bookedUsers'];

    $nonBookedUsers = $totalUsers - $bookedUsers;

    // Booked/Non for Each Trip
    $queryTrip = "SELECT t.tripTitle, 
                    COUNT(b.bookingId) AS ParticipantsCount, 
                    (t.maxParticipants - COUNT(b.bookingId)) AS NonBookedPlaces, 
                    SUM(b.totalPrice) AS TotalPriceSum
            FROM trips t
            LEFT JOIN bookings b ON t.tripId = b.tripId
            GROUP BY t.tripDate
            ORDER BY t.tripDate DESC
            LIMIT 10";
    $resultTrip = mysqli_query($conn, $queryTrip);

    // Create arrays to store data for each trip
    $tripTitles = [];
    $tripParticipantsCount = [];
    $tripNonBookedPlaces = [];
    $tripTotalPriceSum = [];

    while ($row = mysqli_fetch_assoc($resultTrip)) {
        $tripTitles[] = $row['tripTitle'];
        $tripParticipantsCount[] = $row['ParticipantsCount'];
        $tripNonBookedPlaces[] = $row['NonBookedPlaces'];
        $tripTotalPriceSum[] = $row['TotalPriceSum'];
    }
    
    // Fetch the top 5 rated trips based on average ratings
    $queryTopRatedTrips = "SELECT t.tripTitle, AVG(r.rate) AS AverageRating
                            FROM trips t
                            LEFT JOIN reviews r ON t.tripId = r.tripId
                            GROUP BY t.tripTitle
                            ORDER BY AverageRating DESC
                            LIMIT 5";
    $resultTopRatedTrips = mysqli_query($conn, $queryTopRatedTrips);

    // Create arrays to store trip titles and average ratings
    $tripTitlesR = [];
    $averageRatings = [];

    while ($row = mysqli_fetch_assoc($resultTopRatedTrips)) {
        $tripTitlesR[] = $row['tripTitle'];
        $averageRatings[] = $row['AverageRating'];
    }

    // Close the database connection
    mysqli_close($conn);
?>
