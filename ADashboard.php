<?php
session_start();
include 'dbConnect.php';

// Include the availability update script
include 'AAvailability.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch the logged-in agent's data
$username = $_SESSION['username'];

// Query to fetch the report count
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportCount = intval($_POST['reportCount']);
    $agentID = $_SESSION['username']; // Adjust this based on your session storage
}


// Query to get the agent's name based on the username
$agentQuery = "SELECT agent_name, agent_id FROM agent WHERE agent_id = ?";
$agentStmt = $dbCon->prepare($agentQuery);
$agentStmt->bind_param("s", $username);
$agentStmt->execute();
$agentResult = $agentStmt->get_result();
$agent = $agentResult->fetch_assoc();
$agentName = $agent['agent_name'];
$agentID = $agent['agent_id'];

$agentStmt->close();

// Fetch the total number of houses registered under this agent
$totalHousesQuery = "SELECT COUNT(*) as total FROM house WHERE agent_id = ?";
$totalHousesStmt = $dbCon->prepare($totalHousesQuery);
$totalHousesStmt->bind_param("s", $agentID);
$totalHousesStmt->execute();
$totalHousesResult = $totalHousesStmt->get_result();
$totalHouses = $totalHousesResult->fetch_assoc()['total'];

$totalHousesStmt->close();

// Fetch the number of available and unavailable houses
$availabilityQuery = "SELECT 
    SUM(CASE WHEN house_availability = 'Available' THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN house_availability = 'Unavailable' THEN 1 ELSE 0 END) as unavailable 
    FROM house WHERE agent_id = ?";
$availabilityStmt = $dbCon->prepare($availabilityQuery);
$availabilityStmt->bind_param("s", $agentID);
$availabilityStmt->execute();
$availabilityResult = $availabilityStmt->get_result();
$availabilityData = $availabilityResult->fetch_assoc();
$availableHouses = $availabilityData['available'];
$unavailableHouses = $availabilityData['unavailable'];

$availabilityStmt->close();

// Fetch the guest rental details
$rentalQuery = "SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN rental_status = 'Accepted' THEN 1 ELSE 0 END) as approved, 
    SUM(CASE WHEN rental_status = 'Pending' THEN 1 ELSE 0 END) as pending 
    FROM rental WHERE agent_id = ?";
$rentalStmt = $dbCon->prepare($rentalQuery);
$rentalStmt->bind_param("s", $agentID);
$rentalStmt->execute();
$rentalResult = $rentalStmt->get_result();
$rentalData = $rentalResult->fetch_assoc();
$totalRentals = $rentalData['total'];
$approvedRentals = $rentalData['approved'];
$pendingRentals = $rentalData['pending'];

$rentalStmt->close();
$stmt->close();
$dbCon->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent | Dashboard </title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- To connect with css file-->
    <link rel="stylesheet" href="css/aDashboard.css">
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
                <h3 > 
                    <a href="AProfile.php">
                        <img src="image/profileIcon.png" class="icon"> Profile
                    </a>
                </h3>
                <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
                    <a href="ADashboard.php">
                        <img src="image/exploreIcon.png" class="icon"> Dashboard
                    </a>
                </h3>
                <h3>
                    <a href="AHouse.php">
                        <img src="image/houseIcon.png" class="icon"> House
                    </a>
                </h3>
                <h3>
                    <a href="ARentalRequest.php">
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
    
    <div class="content_section">

        <div class="divider-container">
            <h1>CURRENT</h1>
            <div class="horizontal-line"></div>
        </div>

        <div class="container-wrapper">

            <div class="container">
                <img src="image/regHouseIcon.png" class="icon">
                <div class="text-content">
                    <h3>Total Registered House</h3>
                    <p class="large-text"><?php echo $totalHouses; ?></p>
                    <p>Houses</p>
                    <a href="AHouse.php" class="view-details">View Details ></a>
                </div>
            </div>

            <div class="container table-container">
                <div class="text-content">
                    <table class="full-table">
                        <tr>
                            <td rowspan="2" class="merged-cell">Current House<br> Availability</td>
                            <td class="regular-cell">Unavailable</td>
                            <td class="large-text"><?php echo $unavailableHouses; ?></td>
                        </tr>
                        <tr>
                            <td class="regular-cell">Available</td>
                            <td class="large-text"><?php echo $availableHouses; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
        
        <div class="divider-container">
            <h1>HISTORY</h1>
            <div class="horizontal-line"></div>
        </div>
        <div class="container-wrapper">
            <div class="container table-container">
                <div class="text-content">
                    <table class="full-table history-table">
                        <tr>
                            <td colspan="3" class="merged-cell">GUEST RENTAL</td>
                        </tr>
                        <tr>
                            <td>
                                <p class="large-text"><?php echo $totalRentals; ?></p>
                                <p class="regular-cell">Total Rental</p>
                            </td>
                            <td>
                                <p class="large-text"><?php echo $approvedRentals; ?></p>
                                <p class="regular-cell">Approved</p>
                            </td>
                            <td>
                                <p class="large-text"><?php echo $pendingRentals; ?></p>
                                <p class="regular-cell">Pending</p>
                            </td>
                        </tr>
                        <tr style="background-color: #D9D9D9BF;">
                            <td colspan="3" class="view-details-cell"><a href="ARentalRequest.php">View Details ></a></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="container table-container">
                
                    <table class="full-table history-table">
                        <tr>
                            <td class="merged-cell">REPORT</td>
                        </tr>
                        <tr>
                            <td class="flex-container">
                                <img src="image/totalReportIcon.png" class="icon">
                                <div class="text-content">
                                <p class="large-text">3</p>
                                <p class="regular-cell">Total Report</p>
                                </div>
                            </td>
                        </tr>
                        <tr style="background-color: #D9D9D9BF;">
                            <td class="view-details-cell"><a href="AReport.php">View Details ></a></td>
                        </tr>
                    </table>
            </div>

        </div>
    </div> <!--content section-->

</body>
</html>
