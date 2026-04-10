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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
    
        .in-nav .btn-update {
            margin-right: 10px;
        }

        .form-container {
            background-color: white;
            margin: 20px 20px 20px 200px;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            margin-top: 150px;
        }

        .guestrent table {
            width: 100%;
            border-collapse: collapse;
        }

        .guestrent th, .guestrent td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .guestrent th {
            background-color: #f2f2f2;
        }

        .guestrent td button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .guestrent .accept-btn {
            background-color: #4CAF50;
            color: white;
        }

        .guestrent .reject-btn {
            background-color: #f44336;
            color: white;
        }

        .btn-btn {
            background-color: #104854;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        
        .verified-icon {
            color: green;
        }
        
        .rejected-icon {
            color: red;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            color: #333;
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
            border: 1px solid #ddd;
            margin: 0 4px;
        }

        .pagination a.active {
            background-color: #104854;
            color: white;
            border: 1px solid #4CAF50;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        .pagination .disabled {
            color: #ddd;
            pointer-events: none;
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
                <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
                    <a href="ARentalRequest.php">
                        <img src="image/keyIcon.png" class="icon"> Rental
                    </a>
                    <h4>
                        <a href="ARentalRequest.php"> &gt; Request
                        </a>
                    </h4>
                    <h4 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
                        <a href="ARentalSummary.php"> &gt; Summary
                        </a>
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
        <div class="button">
            <a href="ARentalRequest.php" class="btn-update">↤</a>
        </div>
        <h2>Summary of House Rental</h2>
    </div>
    
    <div class="form-container">
        <form class="guestrent">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Rental ID</th>
                        <th>Guest ID</th>
                        <th>House Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Database connection parameters
                $servername = "localhost";
                $dbusername = "root";
                $dbpassword = "";
                $dbname = "rvhouse";

                // Create connection
                $dbCon = new mysqli($servername, $dbusername, $dbpassword, $dbname);

                // Check connection
                if ($dbCon->connect_error) {
                    die("Connection failed: " . $dbCon->connect_error);
                }

                
                // Attempt select query execution
                $sql = "SELECT r.*, h.house_name
                        FROM rental r 
                        JOIN house h ON r.house_id = h.house_id
                        WHERE r.rental_status='Accepted' AND r.agent_id=?";
                if ($stmt = $dbCon->prepare($sql)) {
                    $stmt->bind_param("s", $agentID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $count = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $count++ . "</td>";
                            echo "<td>" . $row['rental_id'] . "</td>";
                            echo "<td>" . $row['guest_id'] . "</td>";
                            echo "<td>" . $row['house_name'] . "</td>";
                            echo "<td>" . $row['checkin_date'] . "</td>";
                            echo "<td>" . $row['checkout_date'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='lead'><em>No records were found.</em></td></tr>";
                    }
                    $stmt->close();
                } else {
                    echo "ERROR: Could not able to execute $sql. " . $dbCon->error;
                }

                // Close connection
                $dbCon->close();
                ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>
