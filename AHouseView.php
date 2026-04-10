<?php
session_start();
include 'dbConnect.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}



// Fetch the logged-in agent's data
$username = $_SESSION['username'];

// Query to get the agent's name based on the username
$agentQuery = "SELECT agent_name, agent_id FROM agent WHERE agent_id = ?";
$agentStmt = $dbCon->prepare($agentQuery);
$agentStmt->bind_param("s", $username);
$agentStmt->execute();
$agentResult = $agentStmt->get_result();
$agent = $agentResult->fetch_assoc();
$agentName = $agent['agent_name'];
$agentID = $agent['agent_id'];

// Fetch house details based on house_id passed in the URL
$houseID = $_GET['house_id'];

$houseQuery = "SELECT * FROM house WHERE house_id = ?";
$houseStmt = $dbCon->prepare($houseQuery);
$houseStmt->bind_param("s", $houseID);
$houseStmt->execute();
$houseResult = $houseStmt->get_result();
$house = $houseResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent | House View</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Linking to CSS files -->
    <link rel="stylesheet" href="css/aHouseView.css">
    <link rel="stylesheet" href="css/aOuterStructure.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
    </style>
</head> 
<body>   
    <div class="nav_top">
        <nav>
            <div class="left-section">
                <img src="image/optionIcon.png" style="height: 2rem; width: 2rem;">
                <div class="welcomeUser">
                    <p>Welcome, <?php echo htmlspecialchars($agentName); ?></p>
                </div>
            </div>
            <img src="image/logoVV.png" style="height: auto; width: 4rem;">     
        </nav>
    </div>

    <div class="nav_left">
        <nav>
            <br>
            <img src="image/userIcon.png" style="height: 70px; width: 70px;">
            <h2 style="color: #104854; margin:10px;"><b>AGENT</b></h2>
            <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;"><?php echo htmlspecialchars($agentName); ?></p>
            <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;">[ID: <?php echo htmlspecialchars($agentID); ?>]</p>
            <ul>
                <h3> 
                    <a href="AProfile.php">
                        <img src="image/profileIcon.png" class="icon"> Profile
                    </a>
                </h3>
                <h3>
                    <a href="ADashboard.php">
                        <img src="image/exploreIcon.png" class="icon"> Dashboard
                    </a>
                </h3>
                <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
                    <a href="AHouse.php">
                        <img src="image/houseIcon.png" class="icon"> House
                    </a>
                </h3>
                <h3>
                    <a href="ARentalSummary.php">
                        <img src="image/keyIcon.png" class="icon"> Rental
                    </a>
                </h3>
                <h3>
                    <a href="AReport.php">
                        <img src="image/reportIcon.png" class="icon"> Report
                    </a>
                </h3>
                <h3><br>
                    <a href="Logout.php">
                        <img src="image/logoutIcon.png" class="icon"> Log Out
                    </a>
                </h3>
            </ul>   
        </nav>
    </div>

    <div class="in-nav">
        <h2>House Details</h2>
    </div>

    <div class="content_section">
        <div class="actions">
            <button class="update-house" onclick="window.location.href='AHouseUpdate.php?house_id=<?php echo htmlspecialchars($houseID); ?>'">
                <img src="image/AeditIcon.png" alt="Update"> Update
            </button>
        </div>
        <div class="house_details_container">
            <div class="container_header">
                <h2>HOUSE DETAILS</h2>
                <a href="AHouse.php" class="close_button">X</a>
            </div>
            <div class="info_container">
                <div class="house_info">
                    <p><span class="info_label">Name:</span> <?php echo htmlspecialchars($house['house_name']); ?></p>
                    <p><span class="info_label">Address:</span> <?php echo htmlspecialchars($house['house_address']); ?></p>
                    <p><span class="info_label">State:</span> <?php echo htmlspecialchars($house['house_state']); ?></p>
                    <p><span class="info_label">Type:</span> <?php echo htmlspecialchars($house['house_type']); ?></p>
                    <p><span class="info_label">Rate:</span> RM<?php echo htmlspecialchars($house['house_rate']); ?>/night</p>
                    <p><span class="info_label">Details:</span> <?php echo htmlspecialchars($house['house_details']); ?></p>
                </div>
                <div class="photos_container">
                    <h3>House Photos</h3>
                    <div id="house_photos" class="house_photos">
                        <?php
                        // Assuming house_image stores the image paths as a comma-separated string
                        $images = explode(',', $house['house_image']);
                        foreach ($images as $image) {
                            echo "<img src='" . htmlspecialchars($image) . "' class='house_photo'>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

<?php
$agentStmt->close();
$houseStmt->close();
$dbCon->close();
?>
