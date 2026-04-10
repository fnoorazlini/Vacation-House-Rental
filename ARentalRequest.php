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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $rental_id = $_POST['rental_id'];

    // Update rental status
    if ($action == 'accept') {
        $updateStatusQuery = "UPDATE rental SET rental_status = 'Accepted' WHERE rental_id = ?";
    } elseif ($action == 'reject') {
        $updateStatusQuery = "UPDATE rental SET rental_status = 'Rejected' WHERE rental_id = ?";
    }

    $stmt = $dbCon->prepare($updateStatusQuery);
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();
    $stmt->close();

    $dbCon->close();

    // Redirect to rental request page
    header('Location: ARentalRequest.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent | Rental Requests</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/aHouseRegister.css">
    <link rel="stylesheet" href="css/aOuterStructure.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

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
    <div class="form-container">
        <form class="guestrent" method="POST" action="updateRentalStatus.php">
            <?php
            // Include dbConnect file
            require_once "dbConnect.php";

            // Attempt select query execution without pagination
            $sql = "SELECT r.*, p.payment_receipt,h.house_name
                    FROM rental r 
                    JOIN payment p ON r.rental_id = p.rental_id 
                    JOIN house h ON r.house_id = h.house_id
                    WHERE r.agent_id = ? 
                    ORDER BY r.rental_bookingdate DESC";
            $stmt = $dbCon->prepare($sql);
            $stmt->bind_param("s", $agentID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>No.</th>";
                echo "<th>Rental ID</th>";
                echo "<th>Guest ID</th>";
                echo "<th>House Name</th>";
                echo "<th>Booking Date</th>";
                echo "<th>Rental Details</th>";
                echo "<th>Payment Details</th>";
                echo "<th>Actions</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                $count = 1; // Initialize count
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>$count</td>";
                    echo "<td>" . $row['rental_id'] . "</td>";
                    echo "<td>" . $row['guest_id'] . "</td>";
                    echo "<td>" . $row['house_name'] . "</td>";
                    echo "<td>" . $row['rental_bookingdate'] . "</td>";
                    echo "<td><a href='ARequestView.php?rentalId=" . $row['rental_id'] . "' class='btn-btn'>View</a></td>";
                    echo "<td><button type='button' class='view-receipt-btn' data-rental-id='" . $row['rental_id'] . "' data-receipt-url='" . $row['payment_receipt'] . "'>View Receipt</button></td>";
                    echo "<td>";
                    if ($row['rental_status'] == 'Accepted') {
                        echo "<button type='button' class='accept-btn' disabled>Accepted</button>";
                    } elseif ($row['rental_status'] == 'Rejected') {
                        echo "<button type='button' class='reject-btn' disabled>Rejected</button>";
                    } else {
                        echo "<input type='hidden' name='rental_id' value='" . $row['rental_id'] . "'>";
                        echo "<button type='button' class='accept-btn' data-id='" . $row['rental_id'] . "'>Accept</button>";
                        echo "<button type='button' class='reject-btn' data-id='" . $row['rental_id'] . "'>Reject</button>";
                    }
                    echo "</td>";
                    echo "</tr>";
                    $count++;
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p class='lead'><em>No records were found.</em></p>";
            }

            // Close connection
            $stmt->close();
            $dbCon->close();
            ?>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
        $('.accept-btn').click(function () {
            var rentalId = $(this).data('id');
            var form = $(this).closest('form');

            Swal.fire({
                title: 'Accept Rental Request',
                text: 'Are you sure you want to accept this rental request?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, accept it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create hidden inputs
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'action',
                        value: 'accept'
                    }).appendTo(form);
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'rental_id',
                        value: rentalId
                    }).appendTo(form);
                    // Submit the form
                    form.submit();
                }
            });
        });

        $('.reject-btn').click(function () {
            var rentalId = $(this).data('id');
            var form = $(this).closest('form');

            Swal.fire({
                title: 'Reject Rental Request',
                text: 'Are you sure you want to reject this rental request?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create hidden inputs
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'action',
                        value: 'reject'
                    }).appendTo(form);
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'rental_id',
                        value: rentalId
                    }).appendTo(form);
                    // Submit the form
                    form.submit();
                }
            });
        });

        $('.view-receipt-btn').click(function () {
        var receiptUrl = $(this).data('receipt-url');
        var fileExtension = receiptUrl.split('.').pop().toLowerCase();

        if (['jpg', 'jpeg', 'png', 'gif'].indexOf(fileExtension) > -1) {
            // Display image directly
            Swal.fire({
                title: 'Receipt',
                html: '<img src="' + receiptUrl + '" style="max-width: 100%;">',
                confirmButtonText: 'Close',
                confirmButtonColor: '#104854',
                heightAuto: false, // Set heightAuto to false
                width: '40%'
            });
        } else if (fileExtension === 'pdf') {
            // Display PDF using embed tag
            Swal.fire({
                title: 'Receipt',
                html: '<embed src="' + receiptUrl + '" type="application/pdf" style="width: 100%; height: 500px;">',
                confirmButtonText: 'Close',
                confirmButtonColor: '#104854',
                heightAuto: false, // Set heightAuto to false
                width: '40%'
            });
        } else {
            // Handle unsupported file types gracefully
            Swal.fire({
                title: 'Receipt',
                text: 'Unsupported file type. Cannot display the receipt.',
                icon: 'error',
                confirmButtonColor: '#104854',
                heightAuto: false // Set heightAuto to false
            });
        }
    });
    });
    </script>
</body>
</html>
