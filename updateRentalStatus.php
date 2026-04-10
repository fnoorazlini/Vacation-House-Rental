<?php
// Include dbConnect file
require_once "dbConnect.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get rental ID and action from the form submission
    $rental_id = $_POST['rental_id'];
    $action = $_POST['action'];

    // Determine the new status based on the action
    if ($action == 'accept') {
        $new_status = 'Accepted';
    } elseif ($action == 'reject') {
        $new_status = 'Rejected';
    }

    // Update the rental status in the database
    $sql = "UPDATE rental SET rental_status = '$new_status' WHERE rental_id = '$rental_id'";
    
    if (mysqli_query($dbCon, $sql)) {
        echo "Rental status updated successfully.";
    } else {
        echo "ERROR: Could not execute $sql. " . mysqli_error($dbCon);
    }

    // Close connection
    mysqli_close($dbCon);

    // Redirect back to the previous page
    header("Location: ARentalRequest.php");
    exit;
}
?>
