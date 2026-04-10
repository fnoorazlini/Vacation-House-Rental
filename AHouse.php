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

// Query to get the agent's name based on the username
$agentQuery = "SELECT agent_name, agent_id FROM agent WHERE agent_id = ?";
$agentStmt = $dbCon->prepare($agentQuery);
$agentStmt->bind_param("s", $username);
$agentStmt->execute();
$agentResult = $agentStmt->get_result();
$agent = $agentResult->fetch_assoc();
$agentName = $agent['agent_name'];
$agentID = $agent['agent_id'];

// Query to get houses associated with the agent, including images
$sql = "SELECT house_id, house_name, house_type, house_availability, house_rate, house_image 
        FROM house 
        WHERE agent_id = ?";

$stmt = $dbCon->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Handle delete operation if requested
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];

    // Perform delete operation
    $deleteQuery = "DELETE FROM house WHERE house_id = ?";
    $deleteStmt = $dbCon->prepare($deleteQuery);
    $deleteStmt->bind_param("s", $deleteId);

    if ($deleteStmt->execute()) {
        // Set session variable for success message
        $_SESSION['house_deleted'] = true;
    } else {
        echo "Error deleting record: " . $deleteStmt->error;
    }
}

$agentStmt->close();
$stmt->close();
$dbCon->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent | Dashboard</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- To connect with css file-->
    <link rel="stylesheet" href="css/aHouse.css">
    <link rel="stylesheet" href="css/aOuterStructure.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
    </style>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
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

    <div class="in-nav">
        <h2>List of Houses</h2>
    </div>

    <div class="content_section">
        <div class="actions">
            <div class="search-container">
                <input type="text" placeholder="Search house name..." id="searchInput">
                <img src="image/searchIcon.png" alt="Search">
            </div>
            <button class="add-new" onclick="window.location.href='AHouseRegister.php'">
                <img src="image/AhouseIcon.png" alt="Add new"> Add New
            </button>
        </div>
        <div class="house-list">
            <table id="houseTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>House ID</th>
                        <th>House Name</th>
                        <th>Type</th>
                        <th>Availability</th>
                        <th>Rate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $counter++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['house_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['house_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['house_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['house_availability']) . "</td>";
                        echo "<td>RM" . htmlspecialchars($row['house_rate']) . "</td>";
                        echo "<td>
                                <button class='view' onclick=\"window.location.href='AHouseView.php?house_id=" . htmlspecialchars($row['house_id']) . "'\">
                                    <img src='image/viewIcon.png' alt='View'>
                                </button>
                                <button class='delete' onclick=\"confirmDelete('" . htmlspecialchars($row['house_id']) . "', '" . htmlspecialchars($row['house_name']) . "', '" . htmlspecialchars($row['house_image']) . "')\">
                                    <img src='image/binIcon.png' alt='Delete'>
                                </button>
                            </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            let filter = this.value.toUpperCase();
            let rows = document.querySelector("#houseTable tbody").rows;

            for (let i = 0; i < rows.length; i++) {
                let houseName = rows[i].cells[2].textContent.toUpperCase();
                if (houseName.indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        });

        function confirmDelete(houseId, houseName, houseImages) {
            let firstImage = getFirstImage(houseImages);

            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to delete ${houseName}?`,
                imageUrl: firstImage ? firstImage : null, // Display the first image if available
                imageWidth: 400,
                imageHeight: 300,
                imageAlt: 'House image',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to delete script with house ID as parameter
                    window.location.href = `AHouse.php?delete=${houseId}`;
                }
            });
        }

        function getFirstImage(imagesList) {
            let images = imagesList.split(',');
            return images.length > 0 ? images[0].trim() : null;
        }

        <?php if (isset($_SESSION['house_deleted']) && $_SESSION['house_deleted']) { ?>
        Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'The house has been deleted.',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false
        }).then(() => {
            window.location.href = 'AHouse.php'; // Redirect after showing success message
        });
        <?php unset($_SESSION['house_deleted']); ?> // Clear the session variable
        <?php } ?>  
    </script>
</body>
</html>
