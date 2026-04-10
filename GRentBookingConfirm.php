<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Guest | Rent (3)</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/gRentBookingConfirm.css">
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        /* Disable pointer events for links */
        .nav_rent a.disabled {
            pointer-events: none;
            color: grey; /* Optional: change color to indicate it's disabled */
        }
    </style>
</head>
<body>
<?php include('navGuest.php'); ?>

    <div class="header-container">
        <ul class="nav_rent">
            <li><a href="GRentConfirmation.php" class="disabled">1</a></li>
            <li><a href="GRentPaymentInfo.php" class="disabled">2</a></li>
            <li class="active"><a href="GRentBookingConfirm.php" class="disabled">3</a></li>
        </ul>
    </div>

    <div class="success-message-box">
        <img src="image/logoVacayVista2.png">
        <h1>THANK YOU!</h1>      
        <h3>You have done booking for your house!</h3>
        <div class="button">
            <a href="GExplore.php" class="btn-return">✈︎ Homepage</a>
        </div>
    </div>    

</body>
</html>
