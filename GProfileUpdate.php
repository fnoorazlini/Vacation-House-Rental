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
$name_err = $birthdate_err = $gender_err = $address_err = $phone_err = $email_err = "";

// Fetch the user details from the database
$sql = "SELECT guest_id, guest_name, guest_gender, guest_birthOfDate, guest_address, guest_contactNo, guest_email FROM guest WHERE guest_id = ?";
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
            $birthdate = date('Y-m-d', strtotime($birthdate));
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

    // Check input errors before updating the database
    if (empty($name_err) && empty($birthdate_err) && empty($gender_err) && empty($address_err) && empty($phone_err) && empty($email_err)) {
        // Update the user details in the database
        $sql = "UPDATE guest SET guest_name = ?, guest_gender = ?, guest_birthOfDate = ?, guest_address = ?, guest_contactNo = ?, guest_email = ? WHERE guest_id = ?";
        if ($stmt = mysqli_prepare($dbCon, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssss", $name, $gender, $birthdate, $address, $phone, $email, $username);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                 // Set session variable for success message
        $_SESSION['profile_update_success'] = true;
                // Redirect to profile page after successful update
                header("location: GProfile.php");
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
    <meta charset="UTF-8">
    <title>Guest | Update Profile</title>
    <link rel="stylesheet" href="css/gProfileUpdateStyletest.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        .error {
            color: red;
        }
    </style>
</head>

<body>
<?php include('navGuest.php'); ?>
    <div class="content_box">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h1>UPDATE PROFILE INFORMATION</h1>
            <p>Please fill this form to update your details.</p>
            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" value="<?php echo htmlspecialchars($id); ?>" disabled class="form-control"  style="background:none; color:black; border:none;">
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" class="form-control">
                <span class="error"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label for="birthdate">Date of Birth</label>
                <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>" class="form-control">
                <span class="error"><?php echo $birthdate_err; ?></span>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <div id="gender">
                    <input type="radio" id="gender_male" name="gender" value="Male" <?php if ($gender == "Male") echo 'checked="checked"'; ?>>
                    <label for="gender_male">Male</label>
                    <input type="radio" id="gender_female" name="gender" value="Female" <?php if ($gender == "Female") echo 'checked="checked"'; ?>>
                    <label for="gender_female">Female</label>
                </div>
                <span class="error"><?php echo $gender_err; ?></span>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" class="form-control">
                <span class="error"><?php echo $address_err; ?></span>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control">
                <span class="error"><?php echo $phone_err; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control">
                <span class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <div class="button-container">
                    <a href="GProfile.php" class="edit-button">↤ Back</a>
                    <button type="submit" class="edit-button">Update ⟳</button>
                </div>
            </div>
        </form>
    </div>

</body>

</html>


<script>
    document.addEventListener('DOMContentLoaded', (event) => {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
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

        // Validate gender
        const gender = document.querySelectorAll('input[name="gender"]');
        let genderSelected = false;
        gender.forEach((radio) => {
            if (radio.checked) {
                genderSelected = true;
            }
        });
        if (!genderSelected) {
            gender[0].parentElement.nextElementSibling.textContent = "Please select your gender.";
            isValid = false;
        } else {
            gender[0].parentElement.nextElementSibling.textContent = "";
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

