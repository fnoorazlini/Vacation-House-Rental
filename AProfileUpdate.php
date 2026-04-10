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

// Initialize variables
$name = $birthdate = $gender = $address = $phone = $email = "";
$name_err = $birthdate_err = $gender_err = $address_err = $phone_err = $email_err =$bank_err = "";

// Fetch the user details from the database
$sql = "SELECT agent_id, agent_name, agent_gender, agent_birthOfDate, agent_address, agent_contactNo, agent_email, agent_bank FROM agent WHERE agent_id = ?";
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
            mysqli_stmt_bind_result($stmt, $id, $name, $gender, $birthdate, $address, $phone, $email,$bank);
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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST['name']))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST['name']);
    }

    // Validate birthdate
    if (empty(trim($_POST['birthdate']))) {
        $birthdate_err = "Please enter your birthdate.";
    } else {
        $birthdate = date('Y-m-d', strtotime($_POST['birthdate'])); // Convert to Y-m-d format for MySQL
    }

    // Validate gender
    if (empty(trim($_POST['gender']))) {
        $gender_err = "Please select your gender.";
    } else {
        $gender = trim($_POST['gender']);
    }

    // Validate address
    if (empty(trim($_POST['address']))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST['address']);
    }

    // Validate phone number
    if (empty(trim($_POST['phone']))) {
        $phone_err = "Please enter your phone number.";
    } elseif (!is_numeric(trim($_POST['phone']))) {
        $phone_err = "Please enter a valid phone number.";
    } else {
        $phone = trim($_POST['phone']);
    }

    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST['email']);
    }

    // Validate bank
    if (empty(trim($_POST['bank']))) {
        $bank_err = "Please enter your bank account.";
    } else {
        $bank = trim($_POST['bank']);
    }

    // Check input errors before updating the database
    if (empty($name_err) && empty($birthdate_err) && empty($gender_err) && empty($address_err) && empty($phone_err) && empty($email_err)) {
        // Update the user details in the database
        $sql = "UPDATE agent SET agent_name = ?, agent_gender = ?, agent_birthOfDate = ?, agent_address = ?, agent_contactNo = ?, agent_email = ?, agent_bank = ? WHERE agent_id = ?";
        if ($stmt = mysqli_prepare($dbCon, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssss", $name, $gender, $birthdate, $address, $phone, $email, $bank, $username);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to profile page after successful update
                header("location: AProfile.php");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}

// Close connection
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent | Profile </title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- To connect with css file-->
    <link rel="stylesheet" href="css/AOuterStructure.css">
    <link rel="stylesheet" href="css/AProfileUpdate.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        .error { color: red; }
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
            <h2 style="color: #104854; margin:10px;"><b>AGENT</b></h2>
            <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;"><?php echo htmlspecialchars($name); ?></p>
            <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;">[ID: <?php echo htmlspecialchars($id); ?>]</p>
            <ul>
                <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);" > 
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
                    <a href="ARentalSummary.php">
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

    <div class="contentUpdate_section">
        <h1>UPDATE PROFILE INFORMATION</h1><br>
        <div class="content_box">
            <div class="user-icon-container">
                <img src="image/userIcon.png" class="shadow-img">
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <table>
                    <tr>
                        <th>USERNAME</th>
                        <td>: <input type="text" value="<?php echo htmlspecialchars($id); ?>" disabled></td>
                    </tr>
                    <tr>
                        <th>NAME</th>
                        <td>: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
                            <span class="error"><?php echo $name_err; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>BIRTH OF DATE</th>
                        <td>: <input type="date" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>">
                            <span class="error"><?php echo $birthdate_err; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>GENDER</th>
                        <td>: 
                            <select name="gender">
                                <option value="Male" <?php if($gender == "Male") echo 'selected="selected"'; ?>>Male</option>
                                <option value="Female" <?php if($gender == "Female") echo 'selected="selected"'; ?>>Female</option>
                            </select>
                            <span class="error"><?php echo $gender_err; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>ADDRESS</th>
                        <td>: <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
                            <span class="error"><?php echo $address_err; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>PHONE NUMBER</th>
                        <td>: <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                            <span class="error"><?php echo $phone_err; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>EMAIL</th>
                        <td>: <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            <span class="error"><?php echo $email_err; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>BANK ACCOUNT</th>
                        <td>: <input type="text" name="bank" value="<?php echo htmlspecialchars($bank); ?>">
                            <span class="error"><?php echo $bank_err; ?></span>
                        </td>
                    </tr>
                </table>
                <div class="button-container">
                    <a href="AProfile.php" class="edit-button">↤ Back</a>
                    <button type="submit" class="edit-button">Update ⟳</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        let isValid = true;
        
        // Validate name
        const name = document.querySelector('input[name="name"]');
        if (name.value.trim() === "") {
            name.nextElementSibling.textContent = "Please enter your name.";
            isValid = false;
        } else {
            name.nextElementSibling.textContent = "";
        }

        // Validate birthdate
        const birthdate = document.querySelector('input[name="birthdate"]');
        if (birthdate.value.trim() === "") {
            birthdate.nextElementSibling.textContent = "Please enter your birthdate.";
            isValid = false;
        } else {
            birthdate.nextElementSibling.textContent = "";
        }

        // Validate address
        const address = document.querySelector('input[name="address"]');
        if (address.value.trim() === "") {
            address.nextElementSibling.textContent = "Please enter your address.";
            isValid = false;
        } else {
            address.nextElementSibling.textContent = "";
        }

        // Validate phone number
        const phone = document.querySelector('input[name="phone"]');
        if (phone.value.trim() === "") {
            phone.nextElementSibling.textContent = "Please enter your phone number.";
            isValid = false;
        } else if (!/^\d+$/.test(phone.value.trim())) {
            phone.nextElementSibling.textContent = "Please enter a valid phone number.";
            isValid = false;
        } else {
            phone.nextElementSibling.textContent = "";
        }

        // Validate email
        const email = document.querySelector('input[name="email"]');
        if (email.value.trim() === "") {
            email.nextElementSibling.textContent = "Please enter your email.";
            isValid = false;
        } else {
            email.nextElementSibling.textContent = "";
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});

</script>