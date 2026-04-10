<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Retrieve data from GHouseDetails.php
$house_id = $_POST['house_id'] ?? null;
$check_in = $_POST['check_in'] ?? null;
$check_out = $_POST['check_out'] ?? null;

// Validate inputs
if (!$house_id || !$check_in || !$check_out) {
    echo "Invalid data received.";
    exit;
}

// Fetch the house details based on house_id
$sql_house = "SELECT house_id, house_name, house_address, house_state, house_type, house_rate, house_image, house_details, agent_id FROM house WHERE house_id = ?";
$stmt_house = mysqli_prepare($dbCon, $sql_house);
mysqli_stmt_bind_param($stmt_house, "s", $house_id);
mysqli_stmt_execute($stmt_house);
$result_house = mysqli_stmt_get_result($stmt_house);

// Fetch the house details based on house_id
// Fetch the user details from the database
$sql = "SELECT guest_id, guest_name, guest_gender, guest_birthOfDate, guest_address, guest_contactNo, guest_email FROM guest WHERE guest_id = ?";

if ($result_house) {
    $house = mysqli_fetch_assoc($result_house);
    if (!$house) {
        echo "No house found with the provided ID.";
        exit;
    }
} else {
    echo "Error fetching house details: " . mysqli_error($dbCon);
    exit;
}

// Calculate total number of nights
$check_in_date = new DateTime($check_in);
$check_out_date = new DateTime($check_out);
$diff = $check_in_date->diff($check_out_date);
$nights_count = $diff->days;

// Calculate rental fee
$rental_fee = $nights_count * $house['house_rate'];

// Calculate deposit (10% of house rate)
$deposit = 0.1 * $house['house_rate'];

// Calculate total amount (rental fee + deposit)
$total_amount = $rental_fee + $deposit;

// Close statement and connection
mysqli_stmt_close($stmt_house);
mysqli_close($dbCon);

// Store necessary details in session
$_SESSION['rental_details'] = array(
    'agent_id' => $house['agent_id'],
    'house_id' => $house['house_id'],
    'house_name' => $house['house_name'],
    'check_in' => $check_in,
    'check_out' => $check_out,
    'nights_count' => $nights_count,
    'house_rate' => $house['house_rate'],
    'rental_fee' => $rental_fee,
    'deposit' => $deposit,
    'total_amount' => $total_amount,
);

$imagePaths = explode(',', $house['house_image']);
$firstImage = $imagePaths[0];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/gRentConfirmation.css">
    <title>Rent Confirmation</title>
    <style>
           /* Your existing CSS styles */
           .confirmation {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 70%;
            margin: auto;
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .confirmation h2 {
            color: #092030;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .flex-container {
            display: flex;
            align-items: center; /* Align items vertically */
        }

        .house-image {
            flex: 1; /* Take up remaining space */
            margin-right: 20px;
            text-align: center; /* Center align image horizontally */
        }

        .house-image img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            vertical-align: middle; /* Ensure image is vertically aligned */
        }

        .confirmation-table {
            flex: 2; /* Take up 2/3 of the flex container */
            width: 100%;
        }

        .confirmation-table td {
            padding: 8px;
            text-align: left;
            font-size: 16px;
            line-height: 1.6;
        }

        .confirmation-table td:first-child {
            font-weight: bold;
        }

        .button-container {
            text-align: center;
            margin-top: 5%;
        }

        .btn-confirm {
            padding: 12px 40px;
            border: 1px solid #092030;
            border-radius: 6px;
            text-decoration: none;
            background: linear-gradient(180deg, #CFE1E4 1%, #F9FDFE 81.5%);
            color: #104854;
            font-weight: 500;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-confirm:hover {
            text-decoration: none;
            color: #ffffff;
            background: linear-gradient(180deg, #123e45 1%, #82b9c9 81.5%);
        }
        
        .nav_rent a.disabled {
            pointer-events: none;
            color: grey; 
        }
    </style>
</head>

<body>
    <?php include('navGuest.php'); ?>
    <div class="header-container">
        <ul class="nav_rent">
            <li class="active"><a href="GRentConfirmation.php" class="disabled">1</a></li>
            <li><a href="GRentPaymentInfo.php" class="disabled">2</a></li>
            <li><a href="GRentBookingConfirm.php" class="disabled">3</a></li>
        </ul>
    </div>


    <div class="confirmation">
        <h2>Rent Confirmation: <?php echo htmlspecialchars($house['house_name']); ?>
        [<?php echo htmlspecialchars($house['house_id']); ?>]</h2><br>
        <div class="flex-container">
            <div class="house-image">
                <img src="<?php echo $firstImage; ?>" alt="House Image">
            </div>
            <table class="confirmation-table">
                <tr>
                    <td>Agent ID:</td>
                    <td><?php echo htmlspecialchars($house['agent_id']); ?></td>
                </tr>
                <tr>
                    <td>Check-In:</td>
                    <td><?php echo (new DateTime($check_in))->format('d/m/y'); ?></td>
                </tr>
                <tr>
                    <td>Check-Out:</td>
                    <td><?php echo (new DateTime($check_out))->format('d/m/y'); ?></td>
                </tr>

                <tr>
                    <td>Number of nights:</td>
                    <td><?php echo $nights_count; ?></td>
                </tr>
                <tr>
                    <td>House Rate:</td>
                    <td>RM <?php echo htmlspecialchars($house['house_rate']); ?></td>
                </tr>
                <tr>
                    <td>Rental Fee:</td>
                    <td>RM <?php echo $rental_fee; ?></td>
                </tr>
                <tr>
                    <td>Deposit (10%):</td>
                    <td>RM <?php echo $deposit; ?></td>
                </tr>
                <tr>
                    <td>Total Amount:</td>
                    <td>RM <?php echo $total_amount; ?></td>
                </tr>
            </table>
            
        </div>
        <div class="button-container">
            <a href="GPaymentInfo.php" class="btn-confirm">Proceed to Payment</a>
        </div>
    </div>

</body>

</html>