<?php
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include the database connection file
require_once("dbConnect.php");

// Get the current logged-in user's username from the session
$username = $_SESSION['username'];

// Fetch the user details from the database
$sql = "SELECT agent_id, agent_name, agent_gender, agent_birthOfDate, agent_address, agent_contactNo, agent_email FROM agent WHERE agent_id = ?";
if ($stmt = mysqli_prepare($dbCon, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_username);

    // Set parameters
    $param_username = $username;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);

        // Check if the user exists, if yes then fetch the details
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $id, $name, $gender, $birthdate, $address, $phone, $email);
            mysqli_stmt_fetch($stmt);

            // Format the birthdate to dd/mm/yyyy
            $birthdate = date('d/m/Y', strtotime($birthdate));
        } else {
            // User doesn't exist
            echo "User doesn't exist.";
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Pagination setup
$results_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Fetch feedback data for the current agent's houses
$sql_feedback = "SELECT house_id, feedback_rating, feedback_comment 
        FROM feedback
        WHERE house_id IN (SELECT house_id FROM house WHERE agent_id = ?)
        LIMIT ?, ?";

if ($stmt_feedback = mysqli_prepare($dbCon, $sql_feedback)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt_feedback, "sii", $param_agent_id, $param_offset, $param_results_per_page);

    // Set parameters
    $param_agent_id = $id;
    $param_offset = $offset;
    $param_results_per_page = $results_per_page;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt_feedback)) {
        $result = mysqli_stmt_get_result($stmt_feedback);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt_feedback);
}

// Total number of pages for pagination
$sql_count = "SELECT COUNT(*) AS total FROM feedback WHERE house_id IN (SELECT house_id FROM house WHERE agent_id = ?)";
if ($stmt_count = mysqli_prepare($dbCon, $sql_count)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt_count, "s", $param_agent_id_count);

    // Set parameters
    $param_agent_id_count = $id;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt_count)) {
        $result_count = mysqli_stmt_get_result($stmt_count);
        $row_count = mysqli_fetch_assoc($result_count);
        $total_pages = ceil($row_count['total'] / $results_per_page);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt_count);
}

// Close database connection
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest | Explore</title>
    <link rel="stylesheet" href="css/guestStyle.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        .content_section_title {
            background-color: white;
            margin-top: 55px;
            margin-left: 150px;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            width: calc(100% - 163px);
            height: 60px;
            padding: 10px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .content_section_title a {
            position: absolute;
        }
        .content_section_title div {
            width: 100%;
            text-align: center;
            color: #273233;
            font-size: 20px;
        }
        .content_section_report {
            background-color: white;
            margin-top: 55px;
            margin-left: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80%;
            height: 70%;
            border-radius: 10px;
        }
        table {
            text-align: center;
            width: 100%;
            border: #273233;

        }
        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #90cad8;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:nth-child(odd) {
            background-color: #ffffff;
        }
        tr.row1 { background-color:#4d7a85; }
        tr.row2 { background-color: #90cad8; }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            margin-left: 120px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #90cad8;
            color: #273233;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .pagination a.active {
            background-color: #4d7a85;
            color: white;
        }

        .btn {
            text-decoration: none;
            color: #1f244a;
            background-color: lightblue;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer; /* Ensure the button shows as clickable */
        }
        .generate-report {
            margin-left: 1120px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="nav_top">
    <nav>
        <div class="left-section">
            <img src="image/optionIcon.png" style="height: 2rem; width: 2rem;">
            <div class="welcomeUser">
                <p>Welcome, <?php echo htmlspecialchars($name); ?></p>
            </div>
        </div>
        <img src="image/logoVV.png" style="height: auto; width: 4rem;">
    </nav>
</div>

<div class="nav_left">
    <nav>
        <br>
        <img src="image/userIcon.png" style="height: 70px; width: 70px;">
        <h2 style="color: #104854;; margin:10px; "><b>AGENT</b></h2>
        <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;"><?php echo htmlspecialchars($name); ?></p>
        <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;">[ID: <?php echo htmlspecialchars($id); ?>]</p>
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
            <h3>
                <a href="ARentalRequest.php">
                    <img src="image/keyIcon.png" class="icon"> Rental
                </a>

            </h3>
            <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
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

<div class="content_section_title">
    <a href="AReport.php">
        <img src="image/back_symbol.png" class="icon">
    </a>
    <div><u>Guest Feedback Report</u></div>
</div>
<div class="generate-report">
    <button class="btn" onclick="generateReport()">Generate Report</button>
</div>
<div class="content_section_report">
    <br>
    <table style="align-items: center;">
        <thead>
        <tr>
            <th>No.</th>
            <th>House ID</th>
            <th>Comment</th>
            <th>Rating</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        while ($row = mysqli_fetch_assoc($result)):
            ?>
            <tr class="<?php echo $count % 2 == 0 ? 'row2' : 'row1'; ?>">
                <td><?php echo $count; ?></td>
                <td><?php echo $row['house_id']; ?></td>
                <td><?php echo $row['feedback_comment']; ?></td>
                <td><?php echo $row['feedback_rating']; ?></td>
            </tr>
            <?php $count++; ?>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<!-- Pagination -->
<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<script>
    function generateReport() {
        const reportWindow = window.open('', '', 'width=800,height=600');
        reportWindow.document.write('<html><head><title>Guest Feedback Report</title></head><body>');
        reportWindow.document.write('<h2>Guest Feedback Report</h2>');
        reportWindow.document.write('<table border="1"><tr><th>House ID</th><th>Feedback Comment</th><th>Feedback Rating</th></tr>');
        <?php
        // Reset the result pointer to the beginning
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)): ?>
        reportWindow.document.write('<tr><td><?php echo htmlspecialchars($row['house_id']); ?></td><td><?php echo htmlspecialchars($row['feedback_comment']); ?></td><td><?php echo htmlspecialchars($row['feedback_rating']); ?></td></tr>');
        <?php endwhile; ?>
        reportWindow.document.write('</table></body></html>');
        reportWindow.document.close();
        reportWindow.print();
    }
</script>
</body>
</html>
