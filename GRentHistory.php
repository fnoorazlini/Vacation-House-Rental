<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Initialize variables
$rentals = [];

// Get the guest ID from session (assuming it's stored as username)
$guest_id = $_SESSION['username']; // Update this based on your session variable name

// Fetch rental details for the logged-in guest where rental status is 'Accepted'
$sql = "SELECT r.rental_id, r.checkin_date, r.checkout_date, r.rental_bookingdate, r.rental_status, h.house_name,
               CASE WHEN f.rental_id IS NOT NULL THEN 'feedback submitted' ELSE '' END AS feedback_status
        FROM rental r
        LEFT JOIN feedback f ON r.rental_id = f.rental_id
        JOIN house h ON r.house_id = h.house_id
        WHERE r.guest_id = ? AND r.rental_status = 'Accepted'
        ORDER BY r.rental_bookingdate DESC";
if ($stmt = mysqli_prepare($dbCon, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_guest_id);

    // Set parameter
    $param_guest_id = $guest_id;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // Fetch all rows as associative array
            while ($row = mysqli_fetch_assoc($result)) {
                $rentals[] = $row; // Add each rental to the rentals array
            }
        } else {
            echo "No rental records found for this guest.";
        }
    } else {
        echo "Error executing SQL statement: " . mysqli_error($dbCon);
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement: " . mysqli_error($dbCon);
}

// Close connection
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental History</title>
    <link rel="stylesheet" href="css/gRentHistory.css"> <!-- Ensure this CSS file exists and styles your page -->
    <style>
        /* Additional styles specific to this page can be added here */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 80%;
            margin: 20px auto;
        }

        h2 {
            color: white;
        }

        .rental-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .rental-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff; /* Add white background to table */
            border-radius: 8px;
            overflow: hidden; /* Ensure rounded corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
        }

        .rental-table th, .rental-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .rental-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .btn-feedback {
            background-color: darkcyan;
            color: #fff;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-feedback:hover {
            background-color: #0d7a8a;
        }
    </style>
</head>
<body>
    <?php include('navGuest.php'); ?>

    <div class="wrapper">
        <h2>Rental History</h2>
        <div class="rental-container">
            <table class="rental-table">
                <thead>
                    <tr>
                        <th>Rental ID</th>
                        <th>House Name</th>
                        <th>Booking Date</th>
                        <th>Check-in Date</th>
                        <th>Checkout Date</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rentals as $rental): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rental['rental_id']); ?></td>
                            <td><?php echo htmlspecialchars($rental['house_name']); ?></td>
                            <td><?php echo htmlspecialchars($rental['rental_bookingdate']); ?></td>
                            <td><?php echo htmlspecialchars($rental['checkin_date']); ?></td>
                            <td><?php echo htmlspecialchars($rental['checkout_date']); ?></td>
                            <td>
                                <?php if (!empty($rental['feedback_status'])): ?>
                                    <?php echo $rental['feedback_status']; ?>
                                <?php else: ?>
                                    <a href="GFeedback.php?rental_id=<?php echo $rental['rental_id']; ?>" class="btn-feedback">Write Feedback</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
