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

// Fetch the guest_id based on username
$sql_guest = "SELECT guest_id FROM guest WHERE guest_id = ?";
if ($stmt_guest = mysqli_prepare($dbCon, $sql_guest)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt_guest, "s", $param_username);

    // Set parameters
    $param_username = $username;

    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt_guest)) {
        // Store result
        mysqli_stmt_store_result($stmt_guest);

        // Check if the guest exists, if yes then fetch the guest_id
        if (mysqli_stmt_num_rows($stmt_guest) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt_guest, $guest_id);
            mysqli_stmt_fetch($stmt_guest);
        } else {
            // Guest doesn't exist
            echo "Guest not found.";
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt_guest);
}

// Initialize variables
$feedback_id_err = $rating_err = $comment_err = "";
$rating = $comment = $rentalid = "";

// Get rental_id from the query parameter
if (isset($_GET['rental_id'])) {
    $rentalid = $_GET['rental_id'];
} else {
    echo "Rental ID not provided.";
    exit;
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate rating
    if (empty(trim($_POST["rating"]))) {
        $rating_err = "Please rate your experience.";
    } else {
        $rating = trim($_POST["rating"]);
    }

    // Validate comment
    if (empty(trim($_POST["comment"]))) {
        $comment_err = "Please enter your comment.";
    } else {
        $comment = trim($_POST["comment"]);
    }

    // Check input errors before inserting in database
    if (empty($rating_err) && empty($comment_err)) {
        // Generate feedbackID (if needed, you can adjust this logic)
        $query = "SELECT MAX(SUBSTRING(feedback_id, 2)) AS max_id FROM feedback";
        $result = mysqli_query($dbCon, $query);
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'];
        $new_id = intval($max_id) + 1;
        $feedback_id = 'F' . sprintf('%03d', $new_id);

        // Prepare an insert statement
        $sql_insert = "INSERT INTO feedback (feedback_id, feedback_rating, feedback_comment, house_id, rental_id) VALUES (?, ?, ?, ?, ?)";
        if ($stmt_insert = mysqli_prepare($dbCon, $sql_insert)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt_insert, "sdsss", $param_feedback_id, $param_rating, $param_comment, $param_houseid, $param_rentalid);

            // Set parameters
            $param_feedback_id = $feedback_id;
            $param_rating = $rating;
            $param_comment = $comment;

            // Fetch house_id based on rental_id
            $sql_houseid = "SELECT house_id FROM rental WHERE rental_id = ?";
            if ($stmt_houseid = mysqli_prepare($dbCon, $sql_houseid)) {
                mysqli_stmt_bind_param($stmt_houseid, "s", $rentalid);
                mysqli_stmt_execute($stmt_houseid);
                mysqli_stmt_bind_result($stmt_houseid, $houseid);
                mysqli_stmt_fetch($stmt_houseid);
                mysqli_stmt_close($stmt_houseid);
            }

            $param_houseid = $houseid;
            $param_rentalid = $rentalid;

            if (mysqli_stmt_execute($stmt_insert)) {
                $_SESSION['feedback_submitted'] = true; // Set session variable for success message
            } else {
                echo "Error: " . $stmt_insert->error;
            }

            // Close statement
            mysqli_stmt_close($stmt_insert);
        }
    }
}

// Fetch house name based on rental_id
$sql_house = "SELECT h.house_name
              FROM rental r
              JOIN house h ON r.house_id = h.house_id
              WHERE r.rental_id = ?";
if ($stmt_house = mysqli_prepare($dbCon, $sql_house)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt_house, "s", $param_rental_id);

    // Set parameters
    $param_rental_id = $rentalid;

    if (mysqli_stmt_execute($stmt_house)) {
        // Store result
        mysqli_stmt_store_result($stmt_house);

        // Check if the house exists, if yes then fetch the house name
        if (mysqli_stmt_num_rows($stmt_house) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt_house, $house_name);
            mysqli_stmt_fetch($stmt_house);
        } else {
            $house_name = "House Not Found";
        }
    } else {
        $house_name = "House Not Found";
    }

    // Close statement
    mysqli_stmt_close($stmt_house);
}

// Close connection
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Guest | Feedback</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- To connect with css file -->
    <link rel="stylesheet" href="css/guestStyle.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        @import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css);

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            overflow: hidden;
            
        }

        .nav_top,
        .nav_left {
            float: left;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .header-container {
            width: 100%;
            height: 60px;
            color: white;
            border: 1px #D9D9D9 transparent;
            overflow: hidden;
            position: fixed;
            top: 75px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .container {
            margin: 150px auto; /* Center horizontally */
            align-items: center;
            width: 45%;
            background: linear-gradient(0deg, #F2FDFF 0%, #E2E2E2 271.67%);
            padding: 3px;
            border-radius: 10px;
            box-sizing: border-box;
            filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25)) inset;
        }

        .feedback_section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center; /* Ensures text alignment center for older browsers */
        }

        fieldset {
            border: none;
        }

        .rating {
            text-align: center;
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            transform: scaleY(1.2);
        }

        .rating>input {
            display: none;
        }

        .rating>label:before {
            content: '\f005';
            font-family: FontAwesome;
            margin: 8px;
            font-size: 36px;
            display: inline-block;
            cursor: pointer;
        }

        .rating>.half:before {
            content: '\f089';
            position: absolute;
            cursor: pointer;
        }

        .rating>label {
            color: rgb(180, 221, 238);
            cursor: pointer;
        }

        .rating>input:checked~label,
        .rating:not(:checked)>label:hover,
        .rating:not(:checked)>label:hover~label {
            color: #e8b90d;
            text-shadow: 0 1.3px #caa72e;
        }

        .rating>input:checked+label:hover,
        .rating>input:checked~label:hover,
        .rating>label:hover~input:checked~label,
        .rating>input:checked~label:hover~label {
            color: #cda14b;
            text-shadow: 0 1.3px #caa72e;
        }

        .rating-label {
            text-align: center;
            font-weight: 600;
            color: black;
        }

        .submit-btn {
            margin-top: 10px;
            background-color: grey;
            color: black;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #007BFF;
            color: white;
        }

        textarea {
            width: 100%;
            height: 90px;
            resize: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <?php include('navGuest.php'); ?>

    <div class="header-container">
        <h1>Feedback for <?php echo htmlspecialchars($house_name); ?></h1>
    </div>

    <div class="container">
        <div class="feedback_section">
            <h2>Rate Your Experience</h2>
            <form id="feedbackForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?rental_id=' . $rentalid); ?>" method="post">
                <fieldset class="rating">
                    <input type="radio" id="star5" name="rating" value="5" /><label class="full" for="star5" title="Awesome - 5 stars"></label>
                    <input type="radio" id="star4half" name="rating" value="4.5" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
                    <input type="radio" id="star4" name="rating" value="4" /><label class="full" for="star4" title="Pretty good - 4 stars"></label>
                    <input type="radio" id="star3half" name="rating" value="3.5" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>
                    <input type="radio" id="star3" name="rating" value="3" /><label class="full" for="star3" title="Meh - 3 stars"></label>
                    <input type="radio" id="star2half" name="rating" value="2.5" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
                    <input type="radio" id="star2" name="rating" value="2" /><label class="full" for="star2" title="Kinda bad - 2 stars"></label>
                    <input type="radio" id="star1half" name="rating" value="1.5" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>
                    <input type="radio" id="star1" name="rating" value="1" /><label class="full" for="star1" title="Sucks big time - 1 star"></label>
                    <input type="radio" id="starhalf" name="rating" value="0.5" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
                </fieldset>
                <textarea name="comment" placeholder="Leave a comment..."></textarea>
                <br>
                <span class="rating-label"><?php echo $rating_err; ?></span>
                <span class="rating-label"><?php echo $comment_err; ?></span>
                <br>
                <input type="submit" class="submit-btn" value="Submit">
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.getElementById('feedbackForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const form = this;
            
            // Validate rating
            const rating = form.elements['rating'].value;
            if (!rating) {
                Swal.fire({
                    icon: 'error',
                    title: 'Rating is required',
                    text: 'Please rate your experience before submitting.'
                });
                return;
            }

            // Validate comment
            const comment = form.elements['comment'].value.trim();
            if (comment === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Comment is required',
                    text: 'Please enter your comment before submitting.'
                });
                return;
            }

            // Submit form if validations pass
            form.submit();
        });

        <?php if (isset($_SESSION['feedback_submitted']) && $_SESSION['feedback_submitted']) { ?>
            setTimeout(function() {
                Swal.fire({
                    title: 'Thank You!',
                    text: 'Feedback submitted successfully.',
                    icon: 'success',
                    showConfirmButton: false,  // Hide the 'OK' button
                    timer: 1000  // Auto-close after 1.5 seconds
                }).then(function() {
                    window.location.href = 'GRentHistory.php';
                });
            }, 100);  // Show for 1 second
            <?php unset($_SESSION['feedback_submitted']); ?> // Clear the session variable
        <?php } ?>
    </script>

</body>
</html>
