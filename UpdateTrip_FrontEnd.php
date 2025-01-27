<?php
include 'UDB.php';


// Check if in edit mode
$editMode = isset($_GET['edit_trip']) && $_GET['edit_trip'] === 'true';

// Initialize variables for prefilling data
$prefilledData = [];
$prefilledCategoryIds = [];

if ($editMode) {
    // Fetch trip data from the database based on trip ID
    $tripId = $_GET['trip_id'];
    $query = "SELECT * FROM trips WHERE tripId = $tripId";
    $result = $mysqli->query($query);

    if ($result) {
        $prefilledData = $result->fetch_assoc(); // Fetch data for prefilling

        // Fetch categories associated with the trip
        $categoryQuery = "SELECT tripCategory FROM trips WHERE tripId = $tripId";
        $categoryResult = $mysqli->query($categoryQuery);

        if ($categoryResult) {
            $categoryRow = $categoryResult->fetch_assoc();
            $prefilledCategoryIds = explode(', ', $categoryRow['tripCategory']);
        }
    } else {
        // Handle database error
        echo "Error fetching trip data: " . $mysqli->error;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data and update/create trip in the database
    $tripTitle = $_POST['tripTitle'];
    $tripLocation = $_POST['tripLocation'];
    $tripDate = $_POST['tripDate'];
    $tripTime = $_POST['tripTime'];
    $tripPrice = $_POST['tripPrice'];
    $maxParticipants = $_POST['maxParticipants'];
    $tripItineraries = $_POST['tripItineraries'];
    $selectedCategories = isset($_POST['tripCategory']) ? $_POST['tripCategory'] : [];

    $categoryString = implode(', ', $selectedCategories);

    if ($editMode) {
        // Perform database update based on trip ID
        $updateQuery = "UPDATE trips SET tripTitle = '$tripTitle', tripLocation = '$tripLocation', 
                        tripDate = '$tripDate', tripTime = '$tripTime', tripPrice = '$tripPrice', 
                        maxParticipants = '$maxParticipants', tripItineraries = '$tripItineraries', 
                        tripCategory = '$categoryString' WHERE tripId = $tripId";
        $updateResult = $mysqli->query($updateQuery);

        if ($updateResult) {
            // Redirect to trips management page after successful update
            header("Location: TripsManagement_FrontEnd.php");
            exit();
        } else {
            // Handle update error
            echo "Error updating trip: " . $mysqli->error;
        }
    } else {
        // Perform database insert for new trip
        $insertQuery = "INSERT INTO trips (tripTitle, tripLocation, tripDate, tripTime, tripPrice, 
                        maxParticipants, tripItineraries, tripCategory) VALUES 
                        ('$tripTitle', '$tripLocation', '$tripDate', '$tripTime', '$tripPrice', 
                        '$maxParticipants', '$tripItineraries', '$categoryString')";
        $insertResult = $mysqli->query($insertQuery);

        if ($insertResult) {
            // Fetch all users' email, firstName, and lastName
            $selectUsers = "SELECT firstName, lastName, emailAddress,enableNotification,verified FROM users";
            $resultUsers = $mysqli->query($selectUsers);

            if ($resultUsers) {
                $mailContent = "
<div style='font-family: Arial, sans-serif;'>
    <h2 style='color: #2C3E50;'>New Trip Added!</h2>
    <h3 style='color: #E74C3C;'>$tripTitle</h3>
    <div>
        <p>We are excited to inform you that a new trip has been added to our list!</p>
        <ul>
            <li><b>Location:</b> $tripLocation</li>
            <li><b>Date:</b> $tripDate</li>
            <li><b>Time:</b> $tripTime</li>
            <li><b>Price:</b> $tripPrice</li>
            <li><b>Maximum Participants:</b> $maxParticipants</li>
        </ul>
        <p>For more details and to book your spot, click <a href='link_to_your_trip_page'>here</a>.</p>
    </div>
    <hr>
    <div dir='rtl'>
        <p>يسرنا أن نبلغكم بأنه تمت إضافة رحلة جديدة إلى قائمتنا!</p>
        <ul>
            <li><b>الموقع:</b> $tripLocation</li>
            <li><b>التاريخ:</b> $tripDate</li>
            <li><b>الوقت:</b> $tripTime</li>
            <li><b>السعر:</b> $tripPrice</li>
            <li><b>الحد الأقصى للمشاركين:</b> $maxParticipants</li>
        </ul>
        <p>لمزيد من التفاصيل ولحجز مكانك، انقر <a href='link_to_your_trip_page'>هنا</a>.</p>
    </div>
    <hr>
    <div dir='rtl'>
        <p>אנו שמחים להודיע לך שנוספה טיול חדש לרשימה שלנו!</p>
        <ul>
            <li><b>מיקום:</b> $tripLocation</li>
            <li><b>תאריך:</b> $tripDate</li>
            <li><b>שעה:</b> $tripTime</li>
            <li><b>מחיר:</b> $tripPrice</li>
            <li><b>מספר משתתפים מרבי:</b> $maxParticipants</li>
        </ul>
        <p>לפרטים נוספים ולהזמנת מקום, לחץ <a href='link_to_your_trip_page'>כאן</a>.</p>
    </div>
</div>";

                while ($user = $resultUsers->fetch_assoc()) {
                    if($user['enableNotification']==1 && $user['verified']==1){
                    sendEmail($user['firstName'], $user['lastName'], $user['emailAddress'], $mailContent);
                    }
                }
            }
            header("Location: UpdateTrip_BackEnd.php");
            exit();
        } else {
            // Handle insert error
            echo "Error creating new trip: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
    <title>Update Trip Details</title>
</head>

<body>
    <?php
    include 'UDB.php';

    // Fetch categories from the tripCategories table using the existing $mysqli connection
    $query = "SELECT categoryName FROM tripcategories";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row["categoryName"];
    }
    ?>

    <?php include 'Header_Owner.php' ?>
    <form method="post"
        action="UpdateTrip_BackEnd.php<?php echo $editMode ? '?edit_trip=true&trip_id=' . $tripId : ''; ?>"
        onsubmit="return confirmUpdate();">
        <br>
        <div class="allinputs">
            <h2 class="addnewtriptitle"><?php echo $editMode ? 'Edit Trip' : 'Insert New Trip'; ?></h2>
            <br><br>
            <div class="input-row">
                <label for="title"> 🌄 Trip Title:</label>
                <input type="text" name="tripTitle" value="<?php echo $editMode ? $prefilledData['tripTitle'] : ''; ?>">
            </div>
            <div class="input-row">
                <label for="location">📍 The Location:</label>
                <select id="location" name="tripLocation">
                    <option value="" disabled>Select a location</option>
                    <?php
                    // Fetch location data from the locations table
                    $locationQuery = "SELECT id, locationName FROM locations ORDER BY locationName ASC";
                    $locationResult = $mysqli->query($locationQuery);

                    while ($locationRow = $locationResult->fetch_assoc()) {
                        $locationId = $locationRow["id"];
                        $locationName = $locationRow["locationName"];

                        // Check if the location is selected (for editing mode)
                        $selected = $editMode && $prefilledData['tripLocation'] == $locationId ? 'selected' : '';

                        echo '<option value="' . $locationId . '" ' . $selected . '>' . $locationName . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="input-row">
                <label for="date">📅 Date:</label>
                <input type="date" name="tripDate" value="<?php echo $editMode ? $prefilledData['tripDate'] : ''; ?>">
            </div>
            <div class="input-row">
                <label for="time">⏰ Time:</label>
                <input type="time" name="tripTime" value="<?php echo $editMode ? $prefilledData['tripTime'] : ''; ?>">
            </div>
            <div class="input-row">
                <label for="price">💳 Price:</label>
                <input type="number" step="0.01" name="tripPrice"
                    value="<?php echo $editMode ? $prefilledData['tripPrice'] : ''; ?>">
            </div>
            <div class="input-row">
                <div class="label-emoji">
                    <span>👥 </span>
                </div>
                <div class="label-text">
                    <label for="max"> Max Participants:</label>
                </div>
                <input type="number" name="maxParticipants"
                    value="<?php echo $editMode ? $prefilledData['maxParticipants'] : ''; ?>">
            </div>
            <div class="input-row">
                <label for="itin">🗺️ Itineraries:</label>
                <textarea
                    name="tripItineraries"><?php echo $editMode ? $prefilledData['tripItineraries'] : ''; ?></textarea>
            </div>
            <div class="input-row">
                <label>🏞️ Categories:</label>
                <select name="tripCategory[]" multiple>
                    <?php
                    // Fetch available categories from the database
                    $categoriesQuery = "SELECT * FROM tripcategories";
                    $categoriesResult = $mysqli->query($categoriesQuery);

                    while ($categoryRow = $categoriesResult->fetch_assoc()) {
                        $categoryId = $categoryRow['categoryId'];
                        $categoryName = $categoryRow['categoryName'];

                        // Check if the category is selected (for editing mode)
                        $selected = $editMode && in_array($categoryId, $prefilledCategoryIds) ? 'selected' : '';

                        echo "<option value='$categoryId' $selected>$categoryName</option>";
                    }
                    ?>
                </select>
            </div>
            <br><br>
            <div class="button-container-updatetrip">
                <button class="canceladdtripbutton" type="button" onclick="cancel()"><i
                        class="fa fa-ban"></i>Cancel</button>
                <button class="addtripbutton"
                    type="submit"><?php echo $editMode ? '<i class="fa fa-floppy-o"></i>Save Changes' : '<i class="fa fa-plus-square-o"></i>Add Trip'; ?></button>
                <audio id="myAudio">
                    <source src="Sounds Effect Audio/popsound.mp3" type="audio/mp3">
                </audio>
            </div>
        </div>
        <br><br>
    </form>
</body>

</html>

<!-- Button Audio -->
<script>
function playAudio() {
    var audio = document.getElementById("myAudio");
    audio.play();
}
</script>

<script>
function cancel() {
    // Redirect to TripsManagement_FrontEnd.php
    window.location.href = 'TripsManagement_FrontEnd.php';
}
</script>

<script>
// Function to confirm updates
function confirmUpdate() {
    var result = confirm("Are you sure you want to save the changes?");
    if (result) {
        // User clicked "OK," proceed with the update action
        playAudio(); // Play a sound effect (if needed)
        return true; // Allow the form submission
    } else {
        // User clicked "Cancel," prevent the form submission
        return false;
    }
}
</script>