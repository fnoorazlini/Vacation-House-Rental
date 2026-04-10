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
$house_id = $_GET['house_id'] ?? null;

if (!$house_id) {
    echo "House ID not provided.";
    exit;
}

// Fetch house details from the database based on house_id
$sql = "SELECT house_name, house_address, house_state, house_type, house_rate, house_image, house_details, agent_id FROM house WHERE house_id = ?";
$stmt = mysqli_prepare($dbCon, $sql);
mysqli_stmt_bind_param($stmt, "s", $house_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $house = mysqli_fetch_assoc($result);
    if (!$house) {
        echo "No house found with the provided ID.";
        exit;
    }
} else {
    echo "Error fetching house details: " . mysqli_error($dbCon);
    exit;
}

// Close statement and connection
mysqli_stmt_close($stmt);

// Fetch booked dates for the house from the rental table
$sql_booked_dates = "SELECT checkin_date, checkout_date FROM rental WHERE house_id = ?";
$stmt_booked_dates = mysqli_prepare($dbCon, $sql_booked_dates);
mysqli_stmt_bind_param($stmt_booked_dates, "s", $house_id);
mysqli_stmt_execute($stmt_booked_dates);
$result_booked_dates = mysqli_stmt_get_result($stmt_booked_dates);

$booked_dates = [];
while ($row = mysqli_fetch_assoc($result_booked_dates)) {
    $booked_dates[] = [
        'checkin_date' => $row['checkin_date'],
        'checkout_date' => $row['checkout_date']
    ];
}

// Close statement
mysqli_stmt_close($stmt_booked_dates);
mysqli_close($dbCon);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Guest | House Details</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/gHouseDetailsStyle.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

.date-search-container {
    display: flex;
    align-items: center;
    margin-top: 10px;
}

.date-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid transparent;
    border-top-right-radius: 10px;
    border-top-left-radius: 10px;
    padding: 10px;
    font-size: 10px;
    box-shadow: 2px 2px 7px rgba(0, 0, 0, 0.402) inset;
    width: 100%;
}

.number-of-nights {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 0.2px solid transparent;
    border-bottom-right-radius: 10px;
    border-bottom-left-radius: 10px;
    padding: 10px;
    font-size: 15px;
    box-shadow: 2px 2px 7px rgba(0, 0, 0, 0.402) inset;
    margin-top: 10px;
}

.date-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    width: 100%;
}

.date-box span {
    font-size: 1.2em;
    font-weight: bold;
}

.button {
    display: flex;
    justify-content: center;
    margin-top: 16px;
    margin-bottom: 20px;
}

.btn-rent {
    padding: 5px 20px;
    border: 1px #092030 solid;
    border-radius: 6px;
    text-decoration: none;
    background: linear-gradient(180deg, #CFE1E4 1%, #F9FDFE 81.5%);
    color: #104854;
    font-weight: 500;
    font-size: 18px;
    cursor: pointer;
}

.btn-rent:hover {
    text-decoration: none;
    color: #ffffff;
    background: linear-gradient(180deg, #123e45 1%, #82b9c9 81.5%);
}

input {
    border: none;
    background: none;
}

.utilities_section {
    margin-top: 20px;
    overflow: hidden;
    height: auto;
}

.details-columns-container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.details-column {
    width: calc(50% - 10px);
    padding: 0 5px;
    box-sizing: border-box;
}

.details-column p {
    margin: 5px 0;
}

.houseName h2 {
    font-size: 25px;
    font-weight: 700;

}

.houseName small {
    font-size: 20px;
    font-weight: normal;
    color: #555;
}

.houseName h2,
.houseName small {
    margin: 0;
}

.houseGallery {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    /* Two columns with equal width */
    grid-gap: 10px;
    /* Adjust the gap between images as needed */
}

.houseGallery img {
    width: 100%;
    /* Ensure images take up full width of their container */
    height: 200px;
    /* Ensure images take up full height of their container */
    object-fit: cover;
    /* Ensure images cover the entire space without stretching */
    cursor: pointer;
    /* Add cursor pointer to indicate clickable */
}

/* Modal styles */
.modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    z-index: 1000;
    padding-top: 50px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.9);
}

.modal-content {
    margin: auto;
    display: block;
    max-width: 100%;
    max-height: 80%;
    object-fit: contain;
    transition: transform 0.2s ease-out;
}


.close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}
    </style>
</head>

<body>
    <?php include('navGuest.php'); ?>
    <div class="house_details">
        <!-- House details display -->
        <div class="left-side">
            <!-- Left side details -->
            <div class="houseName">
                <h2><?php echo $house['house_name']; ?></h2>
                <small> <?php echo $house['house_state']; ?> | <?php echo $house['house_type']; ?></small>
            </div>
            <div class="houseGallery">
                <!-- House image gallery -->
                <?php
                // Display all house images
                $imagePaths = explode(',', $house['house_image']);
                foreach ($imagePaths as $image) {
                    if (file_exists($image)) {
                        echo "<img src='" . $image . "' alt='House Image' onclick='openModal(\"$image\")'>";
                    } else {
                        echo '<p>Image not found: ' . $image . '</p>';
                    }
                }
                ?>
            </div>
        </div>
        <div class="right-side">
            <!-- Right side details including price, date selection, and utilities -->
            <div class="housePriceRate">
                <div class="priceRate_Section">
                    <p>PRICE RATE</p>
                    <h2>RM <?php echo $house['house_rate']; ?> <small>per night</small></h2>
                </div>
                <div class="date-search-container">
                    <!-- Date selection section -->
                    <div class="date-container">
                        <div class="date-box">
                            <span>CHECK-IN:</span>
                            <input id="check-in" name="Check-In" type="date" placeholder="check-in" onchange="checkDateAvailability()">
                        </div>
                        <div class="separator" style="font-size: 20px;">|</div>
                        <div class="date-box">
                            <span>CHECK-OUT:</span>
                            <input id="check-out" name="Check-Out" type="date" placeholder="check-out" onchange="checkDateAvailability()">
                        </div>
                    </div>
                </div>
                <div class="number-of-nights" id="number-of-nights">
                    <small>NO OF NIGHT:</small> <b><span id="nights-count"></span></b>
                </div>
                <div class="button">
                    <!-- Rent form submission -->
                    <form action="GRentConfirmation.php" method="POST" id="rent-form" onsubmit="return validateAndSubmit()">
                        <input type="hidden" name="house_id" value="<?php echo htmlspecialchars($house_id); ?>">
                        <input type="hidden" name="check_in" id="hidden-check-in">
                        <input type="hidden" name="check_out" id="hidden-check-out">
                        <button type="submit" class="btn-rent">Rent</button>
                    </form>
                </div>
            </div>
            <div class="utilities_section">
                <!-- Utilities offered section -->
                <h2>What this place offers</h2>
                <div class="details-columns-container">
                    <?php
                    // Display house details in two columns
                    $details = explode(',', $house['house_details']);
                    $midpoint = ceil(count($details) / 2);
                    ?>
                    <div class="details-column">
                        <?php
                        for ($i = 0; $i < $midpoint; $i++) {
                            echo '<p>•' . trim($details[$i]) . '</p>';
                        }
                        ?>
                    </div>
                    <div class="details-column">
                        <?php
                        for ($i = $midpoint; $i < count($details); $i++) {
                            echo '<p>• ' . trim($details[$i]) . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for displaying images -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImg">
    </div>

    <script>
    // JavaScript for modal functionality
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImg');

    function openModal(imageSrc) {
        modal.style.display = 'block';
        modalImg.src = imageSrc;
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    };

    const checkInInput = document.getElementById('check-in');
    const checkOutInput = document.getElementById('check-out');
    const hiddenCheckIn = document.getElementById('hidden-check-in');
    const hiddenCheckOut = document.getElementById('hidden-check-out');
    const rentForm = document.getElementById('rent-form');
    const nightsCountSpan = document.getElementById('nights-count');

    function updateNightsCount() {
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);
        const timeDiff = checkOutDate - checkInDate;
        const daysDiff = timeDiff / (1000 * 3600 * 24);

        if (!isNaN(daysDiff) && daysDiff > 0) {
            nightsCountSpan.innerText = `${daysDiff} night(s)`;
            hiddenCheckIn.value = checkInInput.value;
            hiddenCheckOut.value = checkOutInput.value;
        } else {
            nightsCountSpan.innerText = 'Please select valid dates';
            hiddenCheckIn.value = '';
            hiddenCheckOut.value = '';
        }
    }

    function checkDateAvailability() {
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);

        if (!checkInDate || !checkOutDate || checkOutDate <= checkInDate) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select valid check-in and check-out dates.'
            });
            return false;
        }

        for (const booking of <?php echo json_encode($booked_dates); ?>) {
            const bookedCheckIn = new Date(booking.checkin_date);
            const bookedCheckOut = new Date(booking.checkout_date);

            if (
                (checkInDate >= bookedCheckIn && checkInDate < bookedCheckOut) ||
                (checkOutDate > bookedCheckIn && checkOutDate <= bookedCheckOut) ||
                (checkInDate <= bookedCheckIn && checkOutDate >= bookedCheckOut)
            ) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'These dates are not available. Please choose other dates.'
                });
                checkInInput.value = '';
                checkOutInput.value = '';
                updateNightsCount();
                return false;
            }
        }

        updateNightsCount();
        return true;
    }

    function validateAndSubmit() {
        if (!checkInInput.value || !checkOutInput.value) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select both check-in and check-out dates before renting.'
            });
            return false;
        }

        if (!checkDateAvailability()) {
            return false;
        }

        return true;
    }
</script>

</body>

</html>