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

// Check existence of id parameter before processing further
if(isset($_GET["rentalId"]) && !empty(trim($_GET["rentalId"]))){
    // Include dbConnect file
    require_once "dbConnect.php";

    // Prepare a select statement
    $sql = "SELECT * FROM rental WHERE rental_id = ?";

    if($stmt = mysqli_prepare($dbCon, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_matricno);
        
        // Set parameters
        $param_matricno = trim($_GET["rentalId"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $rentalid = $row["rental_id"];
                $checkin = $row["checkin_date"];
                $checkout = $row["checkout_date"];
                $bookingdate = $row["rental_bookingdate"];
                $rentalstatus = $row["rental_status"];
                $depo = $row["deposit"];
                $houserate = $row["house_rate"];
                $fullpayment = $row["full_payment"];
                $guestid = $row["guest_id"];
                $agentid = $row["agent_id"];
                $houseid = $row["house_id"];

            } else{
                // URL doesn't contain valid id. Redirect to error page
                header("location: error.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
        $infoQuery = "SELECT a.agent_name, g.guest_name, g.guest_contactNo, g.guest_email, h.house_name
                        FROM rental r 
                        JOIN guest g ON r.guest_id = g.guest_id 
                        JOIN agent a ON r.agent_id = a.agent_id 
                        JOIN house h ON r.house_id = h.house_id
                        WHERE r.rental_id = ?";
        $infoStmt = $dbCon->prepare($infoQuery);
        $infoStmt->bind_param("s", $rentalid);
        $infoStmt->execute();
        $infoResult = $infoStmt->get_result();
        $info = $infoResult->fetch_assoc();

        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($dbCon);
    } else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent | Dashboard</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/aHouseRegister.css">
    <link rel="stylesheet" href="css/aOuterStructure.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        .form-container {
            background-color: white;
            margin: 20px 20px 20px 200px;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            margin-top: 150px;
        }

        .receipt {
            width: 60%;
            margin-top: 150px ;
            margin-left: 360px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }

        .receipt .section {
            margin-bottom: 15px;
        }

        .receipt .section label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .receipt .section .data {
            color: #555;
        }

        .receipt .line {
            border-top: 1px solid black;
            margin: 5px 0;
        }

        .receipt .flex-container {
            display: flex;
            justify-content: space-between;
        }

        .receipt .flex-item {
            width: 48%;
        }

        .receipt .full-payment {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            padding: 5px 0;
            margin: 5px 0;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
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
                <h3>
                    <a href="AHouse.php">
                        <img src="image/houseIcon.png" class="icon"> House
                    </a>
                </h3>
                <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
                    <a href="ARentalRequest.php">
                        <img src="image/keyIcon.png" class="icon"> Rental
                    </a>
                    <h4 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
                        <a href="ARentalRequest.php"> &gt; Request</a>
                    </h4>
                    <h4>
                        <a href="ARentalSummary.php"> &gt; Summary</a>
                    </h4>
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
        <h2>Guest Rental Request</h2>
    </div>

    <div class="wrapper">
        <div class="receipt">
            <div class="flex-container">
                <div class="flex-item section">
                    <label>Rental ID:</label>
                    <div class="data"><?php echo $rentalid; ?></div>
                </div>
                <div class="flex-item section">
                    <label>Booking Date:</label>
                    <div class="data"><?php echo $bookingdate; ?></div>
                </div>
            </div>
            <div class="line"></div>
            <div class="flex-container ">
                <div class="flex-item section">
                    <label>Guest ID:</label>
                    <div class="data"><?php echo $guestid; ?></div>
                </div>
                <div class="flex-item section">
                    <label>Agent ID:</label>
                    <div class="data"><?php echo $agentid; ?></div>
                </div>
            </div>
            <div class="flex-container ">
                <div class="flex-item section">
                    <label>Guest Name:</label>
                    <div class="data"><?php echo htmlspecialchars($info['guest_name']); ?></div>
                </div>
                <div class="flex-item section">
                    <label>Agent Name:</label>
                    <div class="data"><?php echo htmlspecialchars($info['agent_name']); ?></div>
                </div>
            </div>
            <div class="section">
                <label>Guest Phone Number:</label>
                <div class="data"><?php echo htmlspecialchars($info['guest_contactNo']); ?></div>
            </div>
            <div class="section">
                <label>Guest Email:</label>
                <div class="data"><?php echo htmlspecialchars($info['guest_email']); ?></div>
            </div>
            <div class="line"></div>
            <div class="section">
                <label>House Name:</label>
                <div class="data"><?php echo htmlspecialchars($info['house_name']); ?></div>
            </div>
            <div class="flex-container">
                <div class="flex-item section">
                    <label>Checkin Date:</label>
                    <div class="data"><?php echo $checkin; ?></div>
                </div>
                <div class="flex-item section">
                    <label>Checkout Date:</label>
                    <div class="data"><?php echo $checkout; ?></div>
                </div>
            </div>
            <div class="flex-container">
                <div class="flex-item section">
                    <label>House Rate:</label>
                    <div class="data">RM <?php echo $houserate; ?></div>
                </div>
                <div class="flex-item section">
                    <label>Deposit:</label>
                    <div class="data">RM <?php echo $depo; ?></div>
                </div>
            </div>
            <div class="section full-payment">
                <label>Full Payment:</label>
                <div class="data">RM <?php echo $fullpayment; ?></div>
            </div>
            <p><a href="ARentalRequest.php" class="btn-primary">Back</a></p>
        </div>
    </div>
</body>
</html>
