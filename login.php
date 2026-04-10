<?php

// Include config file
require_once("dbConnect.php");

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = md5(trim($_POST["password"])); // Assuming password is stored as MD5 hash
    }

    // Check input errors before processing further
    if (empty($username_err) && empty($password_err)) {
        // Prepare SQL statements for agent and guest
        $agentSql = "SELECT agent_name AS name, 'agent' AS role FROM agent WHERE agent_id = ? AND agent_password = ?";
        $guestSql = "SELECT guest_name AS name, 'guest' AS role FROM guest WHERE guest_id = ? AND guest_password = ?";

        // Attempt to prepare and execute SQL statements
        if ($stmt = mysqli_prepare($dbCon, $agentSql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $username, $password);

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if agent exists
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $name, $role);
                    mysqli_stmt_fetch($stmt);

                    // Start session, set session variables
                    session_start();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['user_role'] = $role;
                    $_SESSION['user_name'] = $name;

                    // Set success message
                    echo "<script>alert('Success login as An Agent!');</script>";

                    // Redirect to agent dashboard
                    header("refresh:1;url=ADashboard.php");
                    exit();
                } else {
                    // If no agent, check guest table
                    if ($stmt = mysqli_prepare($dbCon, $guestSql)) {
                        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
                        if (mysqli_stmt_execute($stmt)) {
                            mysqli_stmt_store_result($stmt);
                            if (mysqli_stmt_num_rows($stmt) > 0) {
                                mysqli_stmt_bind_result($stmt, $name, $role);
                                mysqli_stmt_fetch($stmt);

                                // Start session, set session variables
                                session_start();
                                $_SESSION['loggedin'] = true;
                                $_SESSION['username'] = $username;
                                $_SESSION['user_role'] = $role;
                                $_SESSION['user_name'] = $name;

                                // Set success message
                                echo "<script>alert('Success login as A Guest!');</script>";

                                // Redirect to guest explore page
                                header("refresh:1;url=GExplore.php");
                                exit();
                            } else {
                                $message = 'Wrong Username and Password';
                            }
                        } else {
                            echo "Something went wrong. Please try again later.";
                        }
                    }
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }

        // Close connection
        mysqli_close($dbCon);
    }
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
        body {
            margin: 0;
            padding: 0;
            background-color: #104854;
            color: white;
            font-family: Arial, sans-serif;
        }
        main {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        main::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .hero {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
        }

        .loginform {
            display: flex;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .loginform .bgLogin img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .loginform .form-container {
            padding: 20px;
            max-width: 400px;
            width: 100%;
        }

        .loginform h1 {
            color: #104854;
            margin: 20px 0;
            text-align: center;
        }

        .input-group {
            position: relative;
            margin: 10px 0;
        }

        .input-group input {
            width: 80%;
            height: 50px;
            border-radius: 6px;
            font-size: 18px;
            padding: 0 10px;
            border: 1px solid #1047548e;
            background: transparent;
            color: #104854;
            outline: none;
        }

        .input-group label {
            position: absolute;
            top: 50%;
            left: 60px;
            transform: translateY(-50%);
            color: #104854;
            font-size: 15px;
            pointer-events: none;
            transition: 0.3s;
        }

        input:focus {
            border: 2px solid #18ffff;
        }

        input:focus~label,
        input:valid~label {
            top: 0;
            left: 20px;
            font-size: 16px;
            padding: 0 2px;
            background: #fff;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color:#104854;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 20px 0;
        }

        button[type="submit"]:hover {
            background-color: #005f8a;
        }

        p {
            color: #104854;
        }

        footer p {
            color: white;
        }

        .error,
        .message {
            color: red;
            font-size: 14px;
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
            <div class="loginform">
                <div class="bgLogin">
                    <img src="image/bgLogin.png" alt="login">
                </div>
                <div class="form-container">
                    <h1>LOG IN</h1>
                    <p class="message"><?php echo htmlspecialchars($message); ?></p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="input-group">
                            <input type="text" id="username" name="username" required>
                            <label for="username">Username</label>
                        </div>
                        <p class="message"><?php echo !empty($username_err) ? '<span class="error">' . htmlspecialchars($username_err) . '</span>' : ''; ?></p>

                        <div class="input-group">
                            <input type="password" id="password" name="password" required>
                            <label for="password">Password</label>
                        </div>
                        <p class="message"><?php echo !empty($password_err) ? '<span class="error">' . htmlspecialchars($password_err) . '</span>' : ''; ?></p>

                        <button type="submit">Log In</button>
                        <p>Doesn't have an account? <a href="signUp.php">Sign up</a></p>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="logo">
            <img src="image/logo_ vacayVista.png" alt="VacayVista">
        </div>
        <nav>
            <a href="#">About Us</a>
            <a href="#">Legal</a>
            <a href="#">Privacy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Blog</a>
        </nav>
        <p>copyright &copy; 2024 VacayVista.com</p>
    </footer>
</body>

</html>
