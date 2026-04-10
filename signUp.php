<?php
require_once("dbConnect.php"); // Include your database connection file

// Initialize variables
$username = $password = $role = $name = $birthdate = $gender = $address = $phone = $email = "";
$username_err = $password_err = $role_err = $name_err = $birthdate_err = $gender_err = $address_err = $phone_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate role
    if (isset($_POST["role"]) && !empty(trim($_POST["role"]))) {
        $role = trim($_POST["role"]);
    } else {
        $role_err = "Please select a role.";
    }

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } elseif (!preg_match("/^[a-zA-Z@. ]*$/", $_POST["name"])) {
        $name_err = "Name can only contain letters, spaces, '.', and '@'.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate birthdate
    if (empty(trim($_POST["birthdate"]))) {
        $birthdate_err = "Please enter your birthdate.";
    } else {
        $birthdate = trim($_POST["birthdate"]);
    }

    // Validate gender
    if (isset($_POST["gender"])) {
        $gender = trim($_POST["gender"]);
        // Process further validation and handling
    } else {
        $gender_err = "Please select your gender.";
    }

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } elseif (!is_numeric(trim($_POST['phone']))) {
        $phone_err = "Please enter a valid phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);

        // Check if email already exists in guest table
        $sql_guest = "SELECT guest_id FROM guest WHERE guest_email = ?";
        $stmt_guest = mysqli_prepare($dbCon, $sql_guest);
        if ($stmt_guest) {
            mysqli_stmt_bind_param($stmt_guest, "s", $email);
            mysqli_stmt_execute($stmt_guest);
            mysqli_stmt_store_result($stmt_guest);
            if (mysqli_stmt_num_rows($stmt_guest) > 0) {
                $email_err = "This email is already registered.";
            }
            mysqli_stmt_close($stmt_guest);
        } else {
            echo "Something went wrong with the guest table query.";
        }

        // Check if email already exists in agent table
        $sql_agent = "SELECT agent_id FROM agent WHERE agent_email = ?";
        $stmt_agent = mysqli_prepare($dbCon, $sql_agent);
        if ($stmt_agent) {
            mysqli_stmt_bind_param($stmt_agent, "s", $email);
            mysqli_stmt_execute($stmt_agent);
            mysqli_stmt_store_result($stmt_agent);
            if (mysqli_stmt_num_rows($stmt_agent) > 0) {
                $email_err = "This email is already registered.";
            }
            mysqli_stmt_close($stmt_agent);
        } else {
            echo "Something went wrong with the agent table query.";
        }
    }

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);

        // Check if username already exists in guest table
        $sql_guest = "SELECT guest_id FROM guest WHERE guest_id = ?";
        $stmt_guest = mysqli_prepare($dbCon, $sql_guest);
        if ($stmt_guest) {
            mysqli_stmt_bind_param($stmt_guest, "s", $username);
            mysqli_stmt_execute($stmt_guest);
            mysqli_stmt_store_result($stmt_guest);
            if (mysqli_stmt_num_rows($stmt_guest) > 0) {
                $username_err = "This username is already taken.";
            }
            mysqli_stmt_close($stmt_guest);
        } else {
            echo "Something went wrong with the guest table query.";
        }

        // Check if username already exists in agent table
        $sql_agent = "SELECT agent_id FROM agent WHERE agent_id = ?";
        $stmt_agent = mysqli_prepare($dbCon, $sql_agent);
        if ($stmt_agent) {
            mysqli_stmt_bind_param($stmt_agent, "s", $username);
            mysqli_stmt_execute($stmt_agent);
            mysqli_stmt_store_result($stmt_agent);
            if (mysqli_stmt_num_rows($stmt_agent) > 0) {
                $username_err = "This username is already taken.";
            }
            mysqli_stmt_close($stmt_agent);
        } else {
            echo "Something went wrong with the agent table query.";
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($role_err) && empty($name_err) && empty($birthdate_err) && empty($gender_err) && empty($address_err) && empty($phone_err) && empty($email_err)) {
        // Prepare an insert statement based on role
        if ($role == "guest") {
            $sql = "INSERT INTO guest (guest_id, guest_password, guest_name, guest_gender, guest_birthOfDate, guest_address, guest_contactNo, guest_email, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($dbCon, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssssss", $username, $param_password, $param_name, $param_gender, $param_birthdate, $param_address, $param_phone, $param_email, $param_role);
            }
        } elseif ($role == "agent") {
            $sql = "INSERT INTO agent (agent_id, agent_password, agent_name, agent_gender, agent_birthOfDate, agent_address, agent_contactNo, agent_email, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($dbCon, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssssss", $username, $param_password, $param_name, $param_gender, $param_birthdate, $param_address, $param_phone, $param_email, $param_role);
            }
        }

        // Set common parameters
        $param_password = md5($password); // For demonstration; use bcrypt or better for production
        $param_name = $name;
        $param_gender = $gender;
        $param_birthdate = $birthdate;
        $param_address = $address;
        $param_phone = $phone;
        $param_email = $email;
        $param_role = ($role == "guest") ? 0 : 1; // Assuming 0 for guest role, 1 for agent role

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Registration successful, redirect to login page
            echo "<script>alert('Registration successful. Please login.');</script>";
            echo "<script>location.href='login.php';</script>";
            exit;
        } else {
            echo "Something went wrong. Please try again later.";
        }

        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($dbCon);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VacayVista Homepage</title>
    <link rel="stylesheet" type="text/css" href="css/stylesHome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        main {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
        }

        .hero {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            /* Soft shadow effect */
            text-align: center;
            width: 90%;
            height: 85%;
            max-width: 500px;
            margin: 20px;
        }

        .signUpform {
            text-align: center;
            color: #104854;
            max-width: 100%;
        }

        .signUpform h2 {
            color: #104854;
            font-size: 30px;
            margin-bottom: 30px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: inline-block;
            width: 30%;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .input-group input {
            padding: 10px;
            width: 60%;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            outline: none;
        }

        .input-group select {
            padding: 10px;
            width: 60%;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            outline: none;
        }

        .input-group .gender-options label {
            margin-right: 20px;
            font-weight: normal;
        }

        .input-group .gender-options input[type="radio"] {
            margin-right: 5px;
            vertical-align: middle;
        }

        .input-group .help-block {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

         /* Button styling */
    .submit-button {
        background-color: #104854; /* Background color */
        color: white; /* Text color */
        border: none; /* Remove border */
        padding: 10px 20px; /* Padding */
        border-radius: 5px; /* Rounded corners */
        cursor: pointer; /* Cursor style */
        font-size: 16px; /* Font size */
        transition: background-color 0.3s ease; /* Smooth transition */
    }

    .submit-button:hover {
        background-color: #0c3747; /* Darker background color on hover */
    }

    /* Reset button styling */
    .reset-button {
        background-color: #f3faf9; /* Background color */
        color: #104854; /* Text color */
        border: 1px solid #104854; /* Border */
        padding: 10px 20px; /* Padding */
        border-radius: 5px; /* Rounded corners */
        cursor: pointer; /* Cursor style */
        font-size: 16px; /* Font size */
        transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition */
        margin-left: 10px; /* Space between buttons */
    }

    .reset-button:hover {
        background-color: #104854; /* Darker background color on hover */
        color: white; /* Text color on hover */
    }
        /* Adjustments for layout */
        @media (min-width: 768px) {
            .input-group {
                display: flex;
                justify-content: space-between;
            }

            .input-group label {
                width: 30%;
                /* Adjust label width as needed */
                text-align: left;
                margin-bottom: 0;
            }

            .input-group input,
            .input-group select {
                width: 68%;
                /* Adjust input width as needed */
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <a href="index.php"><img src="image/logo_ vacayVista.png" alt="VacayVista"></a>
        </div>
        <div class="header-buttons">
            <button onclick="location.href='login.php'">Log In / Sign Up</button>
        </div>
    </header>
    <main>
        <div class="hero">
            <div class="signUpform">
                <h2>Sign Up</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="role-selection">
                        <label>Select Role:</label>
                        <label>
                            <input type="radio" name="role" value="guest" <?php echo ($role == "guest") ? "checked" : ""; ?>>
                            Guest
                        </label>
                        <label>
                            <input type="radio" name="role" value="agent" <?php echo ($role == "agent") ? "checked" : ""; ?>>
                            Agent
                        </label><br>
                        <span class="help-block"><?php echo $role_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                        <span class="help-block"><?php echo $username_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
                        <span class="help-block"><?php echo $password_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label>Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
                        <span class="help-block"><?php echo $name_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label>Birthdate</label>
                        <input type="date" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>">
                        <span class="help-block"><?php echo $birthdate_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label>Gender</label>
                        <div class="gender-options">
                            <label>
                                <input type="radio" name="gender" value="male" <?php echo ($gender == "male") ? "checked" : ""; ?>> Male
                            </label>
                            <label>
                                <input type="radio" name="gender" value="female" <?php echo ($gender == "female") ? "checked" : ""; ?>> Female
                            </label>
                        </div>
                        <span class="help-block"><?php echo $gender_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label>Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
                        <span class="help-block"><?php echo $address_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                        <span class="help-block"><?php echo $phone_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <span class="help-block"><?php echo $email_err; ?></span>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="submit-button">Sign Up</button>
                        <button type="reset" class="reset-button">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
