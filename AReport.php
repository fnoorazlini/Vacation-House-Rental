
<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// Fetch the user details from the database
$sql = "SELECT agent_id, agent_name, agent_gender, agent_birthOfDate, agent_address, agent_contactNo, agent_email FROM agent WHERE agent_id = ?";
if ($stmt = mysqli_prepare($dbCon, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_username);

    // Set parameters
    $param_username = $username;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);

        // Check if the user exists, if yes then fetch the details
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $id, $name, $gender, $birthdate, $address, $phone, $email);
            mysqli_stmt_fetch($stmt);

            // Format the birthdate to dd/mm/yyyy
            $birthdate = date('d/m/Y', strtotime($birthdate));
        } else {
            // User doesn't exist
            echo "User doesn't exist.";
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Guest | Explore </title>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- To connect with css file-->
		<link rel="stylesheet" href="css/guestStyle.css">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

            .content_section_title {
        background-color: white;
        margin-top: 55px;
        margin-left: 150px;
        display: flex;
        flex-direction: column;
        align-items: left;
        width: 100%;
        height: 60px;
    }            
    .content_section_report {
    background-image: linear-gradient(to right, rgba(0, 0, 0, 0.938), rgba(39, 39, 39, 0.534)), 
                      url('image/report.png');
    background-size: cover; /* or 'contain', depending on your preference */
    background-repeat: no-repeat;
    background-position: center; /* ensures the image is centered */
    margin-top: 55px;
    margin-left: 280px;
    display: flex;
    flex-direction: column;
    width: 70%;
    height: 200px;
    border-radius: 10px; /* border radius */
    box-shadow: 0 0 10px 5px rgba(255, 255, 255, 0.6); /* white blur border */
    text-align: left;
}
    .link{
        text-decoration: none;
        color: #cbdee2;
    }
        </style>
    </head> 
    </body>   
    <div class="nav_top">
        <nav>
            <div class="left-section">
                <img src="image/optionIcon.png" style="height: 2rem; width: 2rem;">
                <div class="welcomeUser">
                    <p>Welcome, Fatin Noorazlinda</p>
                </div>
            </div>
            <img src="image/logoVV.png" style="height: auto; width: 4rem;">     
        </nav>
    </div>


    <div class="nav_top">
        <nav>
            <div class="left-section">
                <img src="image/optionIcon.png" style="height: 2rem; width: 2rem;">
                <div class="welcomeUser">
                    <p>Welcome, <?php echo htmlspecialchars($name); ?></p>
                </div>
            </div>
            <img src="image/logoVV.png" style="height: auto; width: 4rem;">     
        </nav>
    </div>


    <div class="nav_left">
        <nav>
            <br>
            <img src="image/userIcon.png"  style="height: 70px; width: 70px;">
            <h2 style="color: #104854;; margin:10px; "><b>AGENT</b></h2>
            <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;"><?php echo htmlspecialchars($name); ?></p>
            <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;">[ID: <?php echo htmlspecialchars($id); ?>]</p>
            <ul>
                <h3 > 
                    <a href="AProfile.php">
                        <img src="image/profileIcon.png" class="icon"> Profile
                    </a>
                </h3>
                <h3>
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
                <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);"> 
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
    <div class="content_section_title">
        <div style="color: #273233; font-size: 20px;margin-left: 550px;margin-top: 20px;">
            <u style="padding-left: 90px;">Report</u>
        </div>
    </div>    
        <div class="content_section_report">
            <div style="color:white;font-size: 30px;">
                <h3>&nbsp; Monthly Income Report
                    <br><br>
                </h3>

                <h6 style="font-size: 15px;">
                ...................................................................................................................................................................................................................<br>
                    <a href="AReportMonthly.php" class="link">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;View report >
                    </a>
                </h6>
                
            </div>
        </div>
        <div class="content_section_report">
            <div style="color:white;font-size: 30px;">
                <h3>&nbsp; Guest Feedback Report
                    <br><br>
                </h3>

                <h6 style="font-size: 15px;">
                ...................................................................................................................................................................................................................<br>
                    <a href="AReportFeedback.php" class="link">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;View report >
                    </a>
                </h6>
                
            </div>
        </div>        
        
        <div class="content_section_report">
            <div style="color:white;font-size: 30px;">
                <h3>&nbsp; House Ranking Report
                    <br><br>
                </h3>

                <h6 style="font-size: 15px;">
                ...................................................................................................................................................................................................................<br>
                    <a href="AReportRanking.php" class="link">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;View report >
                    </a>
                </h6>
            </div>
        </div>
        <br>
        <br>
    </div>
    </body>
</html>
