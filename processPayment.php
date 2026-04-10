<?php
session_start();
include 'dbConnect.php'; // Assuming dbConnect.php contains your database connection

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Function to handle receipt upload
function uploadReceipt() {
    $target_dir = "receipts/";
    $target_file = $target_dir . basename($_FILES["receipt_upload"]["name"]);
    $uploadOk = 1;
    $file_type = $_FILES["receipt_upload"]["type"];

    // Check file type (allowing both images and PDFs)
    $allowed_types = array("image/jpeg", "image/png", "image/gif", "application/pdf");
    if (!in_array($file_type, $allowed_types)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF images and PDF files are allowed.";
        return false;
    }

    // Ensure target directory exists or create it
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            echo "Failed to create directory.";
            return false;
        }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        return false;
    }

    // Check file size
    if ($_FILES["receipt_upload"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        return false;
    }

    // If everything is ok, try to upload file
    if (move_uploaded_file($_FILES["receipt_upload"]["tmp_name"], $target_file)) {
        echo "The file " . htmlspecialchars(basename($_FILES["receipt_upload"]["name"])) . " has been uploaded.";
        return $target_file; // Return the file path for database insertion
    } else {
        echo "Sorry, there was an error uploading your file.";
        return false;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    // Retrieve and sanitize POST data
    $checkin_date = $_SESSION['rental_details']['check_in'];
    $checkout_date = $_SESSION['rental_details']['check_out'];
    $rental_bookingdate = date("Y-m-d");
    $rental_status = "Pending";
    $deposit = $_SESSION['rental_details']['deposit'];
    $house_rate = $_SESSION['rental_details']['house_rate'];
    $full_payment = $_SESSION['rental_details']['total_amount'];
    $guest_id = $_SESSION['username'];
    $agent_id = $_POST['agent_id'];
    $house_id = $_POST['house_id'];

    // Generate rental_id
    $query = "SELECT MAX(SUBSTRING(rental_id, 2)) AS max_id FROM rental";
    $result = mysqli_query($dbCon, $query);
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'];
    $new_id = intval($max_id) + 1;
    $rental_id = 'R' . sprintf('%03d', $new_id);

    // Insert rental details into the database
    $rental_query = "INSERT INTO rental (rental_id, checkin_date, checkout_date, rental_bookingdate, rental_status, deposit, house_rate, 
                    full_payment, guest_id, agent_id, house_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $rental_stmt = mysqli_prepare($dbCon, $rental_query);
    mysqli_stmt_bind_param($rental_stmt, "sssssdddsss", $rental_id, $checkin_date, $checkout_date, $rental_bookingdate, $rental_status,
                         $deposit, $house_rate, $full_payment, $guest_id, $agent_id, $house_id);

    if (mysqli_stmt_execute($rental_stmt)) {
        mysqli_stmt_close($rental_stmt);

        // Validate rental_id existence
        $query = "SELECT COUNT(*) AS count FROM rental WHERE rental_id = ?";
        $stmt = mysqli_prepare($dbCon, $query);
        mysqli_stmt_bind_param($stmt, "s", $rental_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];

        mysqli_stmt_close($stmt); // Close statement

        if ($count == 0) {
            echo "Invalid rental ID.";
            exit();
        }

        // Validate and process payment details
        $receipt_upload = uploadReceipt(); // Function to handle receipt upload

        if ($receipt_upload === false) {
            echo "Error uploading receipt.";
            exit();
        }

        // Generate payment_id
        $query = "SELECT MAX(SUBSTRING(payment_id, 2)) AS max_id FROM payment";
        $result = mysqli_query($dbCon, $query);
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'];
        $new_id = intval($max_id) + 1;
        $payment_id = 'P' . sprintf('%03d', $new_id);

        // Retrieve payment amount from POST data
        $payment_amount = isset($_POST['payment_amount']) ? $_POST['payment_amount'] : 0;

        // Insert payment details into the database using prepared statements
        $payment_query = "INSERT INTO payment (payment_id, payment_date, payment_amount, rental_id, payment_receipt) VALUES (?, ?, ?, ?, ?)";
        $payment_stmt = mysqli_prepare($dbCon, $payment_query);
        $payment_date = date("Y-m-d");
        mysqli_stmt_bind_param($payment_stmt, "ssdss", $payment_id, $payment_date, $payment_amount, $rental_id, $receipt_upload);

        if (mysqli_stmt_execute($payment_stmt)) {
            // Payment successfully inserted
            mysqli_stmt_close($payment_stmt);
            mysqli_close($dbCon);
            // Redirect or show success message
            header('Location: GRentBookingConfirm.php');
            exit();
        } else {
            // Error inserting payment
            echo "Error: " . mysqli_error($dbCon);
        }
    } else {
        // Error inserting rental details
        echo "Error: " . mysqli_error($dbCon);
    }
}
?>
