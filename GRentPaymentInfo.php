
<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Retrieve rental details from session
if (isset($_SESSION['rental_details'])) {
    $rental_details = $_SESSION['rental_details'];
} else {
    echo "Rental details not found.";
    exit;
}

// Extract rental details
$house_name = $rental_details['house_name'];
$check_in = $rental_details['check_in'];
$check_out = $rental_details['check_out'];
$nights_count = $rental_details['nights_count'];
$house_rate = $rental_details['house_rate'];
$rental_fee = $rental_details['rental_fee'];
$deposit = $rental_details['deposit'];
$total_amount = $rental_details['total_amount'];

// Include the database connection file
require_once("dbConnect.php");

// Function to generate sequential ID
function generateSequentialpaymentID($prefix, $dbCon)
{
    // Query to get the last used ID from the database
    $query = "SELECT MAX(SUBSTRING(payment_id, 2)) AS max_id FROM payment WHERE payment_id LIKE ?";
    $stmt = mysqli_prepare($dbCon, $query);
    $prefix_param = $prefix . '%';
    mysqli_stmt_bind_param($stmt, "s", $prefix_param);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $max_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);    
    


    // Determine the next sequential ID
    $next_id = intval($max_id) + 1;
    return $prefix . sprintf('%03d', $next_id); // Format to three digits
}

// Function to generate sequential ID
function generateSequentialrentalID($prefix, $dbCon)
{
    // Query to get the last used ID from the database
    $query = "SELECT MAX(SUBSTRING(rental_id, 2)) AS max_id FROM rental WHERE rental_id LIKE ?";
    $stmt = mysqli_prepare($dbCon, $query);
    $prefix_param = $prefix . '%';
    mysqli_stmt_bind_param($stmt, "s", $prefix_param);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $max_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

        // Determine the next sequential ID
        $next_id = intval($max_id) + 1;
        return $prefix . sprintf('%03d', $next_id); // Format to three digits
    }

// Generate payment_id and rental_id
$payment_id = generateSequentialpaymentID('P', $dbCon);
$rental_id = generateSequentialrentalID('R', $dbCon);

// Close database connection
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Guest | Rent (2)</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS link -->
    <link rel="stylesheet" href="css/gRentPaymentInfo.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
    </style>
</head>

<body>
    <?php include('navGuest.php'); ?>
    <div class="header-container">
        <ul class="nav_rent">
            <li><a href="GRentConfirmation.php">1</a></li>
            <li class="active"><a href="GPaymentInfo.php">2</a></li>
            <li><a href="GRentBookingConfirm.php">3</a></li>
        </ul>
    </div>

    <div class="payment-info-box">
        <form action="processPayment.php" method="post" enctype="multipart/form-data">
            <h2>PAYMENT</h2>
            <div class="info-top">
                <div class="form-group">

                </div>

                <div class="form-group">
                    <label>Payment ID:</label>
                    <p><?php echo htmlspecialchars($payment_id); ?></p><br>
                    <label>Date:</label>
                    <p><?php echo date("Y-m-d"); ?></p><br>
                    <label>Guest ID:</label>
                    <p><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?></p><br>
                    <label>Rental ID:</label>
                    <p><?php echo htmlspecialchars($rental_id); ?></p>
                </div>
            </div>
            <div class="bottom-info">
                <h3>PAYMENT DETAILS</h3>
                <table>
                    <tr>
                        <th>House Rate <small class="small-text">[per Day]</small></th>
                        <td class="wide-td">RM <?php echo htmlspecialchars($house_rate); ?></td>
                    </tr>
                    <tr>
                        <th>Number Of Days</th>
                        <td class="wide-td"><?php echo $nights_count; ?></td>
                    </tr>
                    <tr>
                        <th>RENTAL FEE <small class="small-text">[x No of Days]</small></th>
                        <td class="wide-td">RM <?php echo $rental_fee; ?></td>
                    </tr>
                    <tr>
                        <th>DEPOSIT <small class="small-text">[10% per house price]</small></th>
                        <td class="wide-td">RM <?php echo $deposit; ?></td>
                    </tr>
                    <tr class="total-row">
                        <th>TOTAL AMOUNT</th>
                        <td class="wide-td">RM <?php echo $total_amount; ?></td>
                    </tr>
                    <input type="hidden" name="rental_id" value="<?php echo htmlspecialchars($rental_id); ?>">

                </table>
            </div>
            <br>

            <div class="receipt-container">
                <label for="receipt_upload">Attach your receipt here:</label>
                <label class="file-upload-btn" for="receipt_upload">
                    <img src="image/uploadIcon.png" alt="Upload Icon" class="upload-icon"> Upload
                </label>
                <input type="file" id="receipt_upload" name="receipt_upload" accept="image/*">
            </div>

            <div class="button">
                <a href="GRentConfirmation.php" class="btn-back">↤ Back</a>
                <button type="submit" class="btn-submit" name="submit_payment">Submit ↦</button>
            </div>
        </form>
    </div>
</body>

</html>
