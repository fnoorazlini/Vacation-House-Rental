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
$agent_id = $rental_details['agent_id'];
$house_id = $rental_details['house_id'];
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

$infoQuery = "SELECT *
              FROM agent
              WHERE agent_id = ?";
        $infoStmt = $dbCon->prepare($infoQuery);
        $infoStmt->bind_param("s", $agent_id);
        $infoStmt->execute();
        $infoResult = $infoStmt->get_result();
        $info = $infoResult->fetch_assoc();

// Function to generate sequential ID
function generateSequentialID($prefix, $dbCon, $table, $column) {
    // Fetch the latest ID from the database
    $sql = "SELECT $column FROM $table ORDER BY $column DESC LIMIT 1";
    $result = mysqli_query($dbCon, $sql);
    $last_id = mysqli_fetch_assoc($result)[$column];
    
    // Increment and format the ID
    $next_id = isset($last_id) ? intval(substr($last_id, 1)) + 1 : 1;
    return $prefix . sprintf('%03d', $next_id); // Format to three digits
}

// Generate new payment_id and rental_id
$payment_id = generateSequentialID('P', $dbCon, 'payment', 'payment_id');
$rental_id = generateSequentialID('R', $dbCon, 'rental', 'rental_id');

$today_date = date("Y-m-d");
$guest_id = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Close database connection if needed
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
        .nav_rent a.disabled {
            pointer-events: none;
            color: grey; 
        }
        .error-message {
            color: red;
            display: none;
            margin-top: 10px;
        }
        .file-name {
            margin-top: 10px;
            color: green;
        }
    </style>
</head>

<body>
    <?php include('navGuest.php'); ?>
    <div class="header-container">
        <ul class="nav_rent">
            <li><a href="GRentConfirmation.php" class="disabled">1</a></li>
            <li class="active"><a href="GRentPaymentInfo.php" class="disabled">2</a></li>
            <li><a href="GRentBookingConfirm.php" class="disabled">3</a></li>
        </ul>
    </div>
    <div class="payment-info-box">
        <form action="processPayment.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <h2>PAYMENT</h2>
            <div class="info-top">
                <div class="form-group">
                    <p class="guestName"><?php echo htmlspecialchars($guest_id); ?></p><br>
                </div>
                <div class="form-group">
                    <label>Payment ID:</label>
                    <p><?php echo htmlspecialchars($payment_id); ?></p><br>
                    <label>Date:</label>
                    <p><?php echo htmlspecialchars($today_date); ?></p><br>
                    <label>Guest ID:</label>
                    <p><?php echo htmlspecialchars($guest_id); ?></p><br>
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
                    <input type="hidden" name="agent_id" value="<?php echo htmlspecialchars($agent_id); ?>">
                    <input type="hidden" name="house_id" value="<?php echo htmlspecialchars($house_id); ?>">            
                    <input type="hidden" name="rental_id" value="<?php echo htmlspecialchars($rental_id); ?>">
                    <input type="hidden" name="payment_amount" value="<?php echo htmlspecialchars($total_amount); ?>">
                    <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
                    <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
                </table>
            </div>
            <br>
           <p> <b>Bank Account:</b> <?php echo htmlspecialchars($info['agent_bank']); ?><p>

            <div class="receipt-container">
                <label for="receipt_upload">Attach your receipt here:</label>
                <label class="file-upload-btn" for="receipt_upload">
                    <img src="image/uploadIcon.png" alt="Upload Icon" class="upload-icon"> Upload
                </label>
                <input type="file" id="receipt_upload" name="receipt_upload" accept="image/*,application/pdf">
                <p class="error-message" id="error-message">Please upload your receipt before submitting.</p>
                <p class="file-name" id="file-name"></p>
            </div>

            <div class="button">
                <a href="#" class="btn-back" id="backButton">↤ Back</a>
                <button type="submit" class="btn-submit" name="submit_payment">Submit ↦</button>
            </div>
        </form>
    </div>

    <script>
    function validateForm() {
        var receiptInput = document.getElementById('receipt_upload');
        var errorMessage = document.getElementById('error-message');
        var fileNameDisplay = document.getElementById('file-name');
        
        if (receiptInput.files.length === 0) {
            errorMessage.style.display = 'block';
            fileNameDisplay.textContent = '';
            return false;
        } else {
            errorMessage.style.display = 'none';
            fileNameDisplay.textContent = 'File uploaded: ' + receiptInput.files[0].name;
        }
        
        return true;
    }

    document.getElementById('receipt_upload').addEventListener('change', function() {
        var fileNameDisplay = document.getElementById('file-name');
        var errorMessage = document.getElementById('error-message');
        if (this.files.length > 0) {
            fileNameDisplay.textContent = 'File uploaded: ' + this.files[0].name;
            errorMessage.style.display = 'none';
        } else {
            fileNameDisplay.textContent = '';
        }
    });

    document.getElementById('backButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default anchor behavior

        // Create a form element
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'GRentConfirmation.php';

        // Append the hidden inputs with rental details to the form
        var rentalDetails = {
            house_id: '<?php echo htmlspecialchars($house_id); ?>',
            check_in: '<?php echo htmlspecialchars($check_in); ?>',
            check_out: '<?php echo htmlspecialchars($check_out); ?>',
        };

        for (var key in rentalDetails) {
            if (rentalDetails.hasOwnProperty(key)) {
                var hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = key;
                hiddenField.value = rentalDetails[key];

                form.appendChild(hiddenField);
            }
        }

        // Append form to the body and submit it
        document.body.appendChild(form);
        form.submit();
    });
    </script>

</body>

</html>
