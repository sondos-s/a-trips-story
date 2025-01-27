<?php
    session_start(); // Start the session for user authentication

    // Check if the user ID is 9 to determine if it's an owner
    $isOwner = false;
    if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == 9) {
        $isOwner = true;
}
?>

<?php
// Include the database connection file
include 'UDB.php';

// Initialize an array to store team members' data
$teamMembers = array();

// Query to fetch team members' data based on teamId
$sql = "SELECT * FROM aboutteam";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Store each team member's data in the array
        $teamMembers[] = $row;
    }
}
?>

<?php include 'AboutUs_InfoUpdate_BackEnd.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="ViewStyles/ViewStyles.css">
    <link rel="stylesheet" href="ViewStyles/OwnerViewStyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=IM Fell French Canon SC' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=IM Fell English' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Jost' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

    <title>About Us</title>
</head>

<body id="">
    <?php 
        if ($isOwner) {
            include 'Header_Owner.php'; // Include owner header if the user is an owner
        } else {
            include 'Header.php'; // Include regular user header
        }
    ?>
    <div class="compactSidebarEditAbout">
        <?php if ($isOwner): ?>
            <button id="editAboutUsInfo" onclick="openAboutUsPopup()"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Edit About</button>
            <button id="editAboutUsTeam" onclick="openAboutUsTeamPopup()"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Edit Team</button>
        <?php endif; ?>
    </div>

    <div id="containaboutus" style="text-align: center;">
        <h1 class="aboutustitle">About us</h1>
        <p>
            <?php echo $aboutUsText; ?>
        </p>
        <br>
        <a href="HomeScreen.php" class="logo"
            style="text-decoration: none; color: #2E2C2C; font-family:'Bitter'; font-weight:bold; font-size: 18px; text-align: center;">
            So join us and let’s go on a trip... <i class='fa fa-map'></i>&nbsp;<i class="fa fa-mouse-pointer"></i></p></a>
        <br><br>
    </div>
    <div class="team">
        <h2 class="teamtitle">Our Team</h2>
        <?php foreach ($teamMembers as $member): ?>
            <div class="team-member">
            <?php
                // Check if a team member has an image; if not, use default.png
                $imageData = $member['memberImage'];
                if (empty($imageData)) {
                    $imagePath = 'ViewStyles/AboutUsTeam/default.png';
                } else {
                    $imagePath = 'data:image/png;base64,' . base64_encode($imageData);
                }
            ?>
            <img src="<?php echo $imagePath; ?>" alt="<?php echo $member['memberName']; ?>">
                <h3><?php echo $member['memberName']; ?></h3>
                <p><?php echo $member['memberDescription']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="popup-container" id="aboutUsPopup">
        <div class="popup-content" style="width: 400px;">
            <a class="popup-close" onclick="closeAboutUsPopup()">×</a>
            <br><br>
            <h5 class="popup-title" style="font-size: 18px;">Edit About Us Information</h5>
            <br>
            <hr>
            <br>
            <form method="post">
                <div class="form-group-aboutus">
                    <label for="aboutUsText" style="float: left; padding-left: 45px;">About Us Text:</label>
                    <textarea style="height: 100px; width: 300px; font-size: 14px;" id="aboutUsText" name="aboutUsText" required><?php echo $aboutUsText; ?></textarea>
                </div>
                <br><br><br>
                <button class="aboutus-save-btn" id="aboutus-save-btn" type="submit" name="submit">Save Changes</button>
            </form>
            <br><br>
        </div>
    </div>


    <div class="popup-container" id="aboutUsTeamPopup">
        <div class="popup-content" style="width: 400px;">
            <a class="popup-close" onclick="closeAboutUsTeamPopup()">×</a>
            <br><br>
            <h5 class="popup-title" style="font-size: 18px;">Edit Team Member Information</h5>
            <br>
            <hr>
            <br>
            <div class="scrollable-container">
            <form method="post" action="AboutUs_TeamUpdate_BackEnd.php" id="editTeamMemberForm">
                <?php foreach ($teamMembers as $i => $member) { ?>
                    <?php $displayIndex = $i + 1; // Increment $i by 1 to start from 1 ?>
                    <div class="team-member-data">
                        <label for="memberName<?php echo $displayIndex; ?>">Name <?php echo $displayIndex; ?>:</label>
                        <input type="text" id="memberName<?php echo $displayIndex; ?>" name="teamMembers[<?php echo $i; ?>][memberName]" value="<?php echo $member['memberName']; ?>">
                        <textarea id="memberDescription<?php echo $displayIndex; ?>" name="teamMembers[<?php echo $i; ?>][memberDescription]" rows="4"><?php echo $member['memberDescription']; ?></textarea>
                        <input type="hidden" name="teamMembers[<?php echo $i; ?>][teamId]" value="<?php echo $member['teamId']; ?>">
                    </div>
                <?php } ?>
                <br><br>
                <button class="aboutus-save-btn" type="submit" name="editTeamMembers">Save Changes</button>
            </form>
            </div>
            <br><br>
        </div>
    </div>

    <audio id="soundPop">
        <source src="ViewStyles/Sounds Effect Audio/popsound.mp3" type="audio/mpeg">
    </audio>
</body>

</html>
<script>
    // Function to open the About Us popup
    function openAboutUsPopup() {
        document.getElementById("aboutUsPopup").style.display = "block";
    }

    // Function to close the About Us popup
    function closeAboutUsPopup() {
        document.getElementById("aboutUsPopup").style.display = "none";
    }

</script>

<script>
    // Function to open the Our Team popup and populate the form
    function openAboutUsTeamPopup(teamMemberId) {
        document.getElementById("aboutUsTeamPopup").style.display = "block";
    }

    // Function to close the Our Team popup
    function closeAboutUsTeamPopup() {
        document.getElementById("aboutUsTeamPopup").style.display = "none";
    }
</script>