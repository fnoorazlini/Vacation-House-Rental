<?php
session_start();
include 'dbConnect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch the logged-in agent's data
$username = $_SESSION['username'];

// Fetch agent's name for display
$sql = "SELECT agent_name, agent_id FROM agent WHERE agent_id = ?";
$stmt = $dbCon->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$agent = $result->fetch_assoc();
$agent_name = $agent['agent_name'];
$agent_id = $agent['agent_id'];
$stmt->close();

// Initialize error messages
$nameErr = $addressErr = $stateErr = $typeErr = $rateErr = $detailsErr = $imageErr = "";
$isFormValid = true;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $state = $_POST['state'];
    $type = $_POST['type'];
    $rate = $_POST['rate'];
    $details = $_POST['details'];
    $availability = "Available";

    // Validate name
    if (empty($name)) {
        $nameErr = "Name is required";
        $isFormValid = false;
    }

    // Validate address
    if (empty($address)) {
        $addressErr = "Address is required";
        $isFormValid = false;
    }

    // Validate state (no numbers allowed)
    if (empty($state)) {
        $stateErr = "State is required";
        $isFormValid = false;
    } elseif (preg_match('/\d/', $state)) {
        $stateErr = "State cannot contain numbers";
        $isFormValid = false;
    }

    // Validate type
    if (empty($type)) {
        $typeErr = "Type is required";
        $isFormValid = false;
    }

    // Validate rate
    if (empty($rate)) {
        $rateErr = "Rate is required";
        $isFormValid = false;
    } elseif (!is_numeric($rate)) {
        $rateErr = "Rate must be a number";
        $isFormValid = false;
    }

    // Validate details
    if (empty($details)) {
        $detailsErr = "Details are required";
        $isFormValid = false;
    }

    // Validate and handle file uploads
    $uploaded_files = [];
    $target_dir = "upload/";
    $files = $_FILES['upload_input'];

    if (empty($files['name'][0])) {
        $imageErr = "At least one image is required";
        $isFormValid = false;
    } else {
        for ($i = 0; $i < count($files['name']); $i++) {
            $target_file = $target_dir . basename($files["name"][$i]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image or fake image
            $check = getimagesize($files["tmp_name"][$i]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
                $imageErr = "File is not an image.";
                $isFormValid = false;
                break;
            }

            // Check if file already exists
            if (file_exists($target_file)) {
                $uploadOk = 0;
                $imageErr = "Sorry, file already exists.";
                $isFormValid = false;
                break;
            }

            // Check file size
            if ($files["size"][$i] > 5000000) {
                $uploadOk = 0;
                $imageErr = "Sorry, your file is too large.";
                $isFormValid = false;
                break;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $uploadOk = 0;
                $imageErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $isFormValid = false;
                break;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $imageErr = "Sorry, your file was not uploaded.";
                $isFormValid = false;
                break;
            } else {
                if (move_uploaded_file($files["tmp_name"][$i], $target_file)) {
                    $uploaded_files[] = $target_file;
                } else {
                    $imageErr = "Sorry, there was an error uploading your file.";
                    $isFormValid = false;
                    break;
                }
            }
        }
    }

    if ($isFormValid) {
        // Generate houseID
        $query = "SELECT MAX(SUBSTRING(house_id, 2)) AS max_id FROM house";
        $result = mysqli_query($dbCon, $query);
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'];
        $new_id = intval($max_id) + 1;
        $id = 'H' . sprintf('%03d', $new_id);

        if (count($uploaded_files) > 0) {
            $uploaded_files_str = implode(',', $uploaded_files);

            // Insert house data into the database
            $sql = "INSERT INTO house (house_id, house_name, house_address, house_state, house_type, house_rate,
                    house_availability, house_details, house_image, agent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $dbCon->prepare($sql);
            $stmt->bind_param("sssssissss", $id, $name, $address, $state, $type, $rate, $availability, $details, $uploaded_files_str, $agent_id);

            if ($stmt->execute()) {
                $_SESSION['house_registered'] = true; // Set session variable for success message
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent | Dashboard</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- To connect with css file-->
    <link rel="stylesheet" href="css/aHouseRegister.css">
    <link rel="stylesheet" href="css/aOuterStructure.css">
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
                    <p>Welcome, <?php echo htmlspecialchars($agent_name); ?></p>
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
            <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;"><?php echo htmlspecialchars($agent_name); ?></p>
            <p style="color: #325E68; font-size: 1em; margin:0 5px; text-align: left;">[ID: <?php echo htmlspecialchars($agent_id); ?>]</p>
            <ul>
                <h3> 
                    <a href="AProfile.php">
                        <img src="image/profileIcon.png" class="icon"> Profile
                    </a>
                </h3>
                <h3>
                    <a href="ADashboard.php">
                        <img src="image/exploreIcon.png" class="icon"> Dashboard
                    </a>
                </h3>
                <h3 style="background: linear-gradient(90deg, #374E51 0%, #74BBCA 100%);">
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

    <div class="in-nav">
        <h2>House Registration</h2>
    </div>

    <div class="content_section">
        <div class="registration_container">
            <div class="container_header">
                <h2>HOUSE REGISTRATION</h2>
                <a href="AHouse.php" class="close_button">X</a>
            </div>
            <div class="form_container">
                <form id="houseForm" method="POST" enctype="multipart/form-data">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                    <span class="error"><?php echo $nameErr; ?></span>
                    
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address ?? ''); ?>" required>
                    <span class="error"><?php echo $addressErr; ?></span>
                    
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($state ?? ''); ?>" required>
                    <span class="error"><?php echo $stateErr; ?></span>
                    
                    <label for="type">Type</label>
                    <div class="select_container">
                        <select id="type" name="type" required>
                            <option value="Apartment" <?php if (isset($type) && $type == 'Apartment') echo 'selected'; ?>>Apartment</option>
                            <option value="Condominium" <?php if (isset($type) && $type == 'Condominium') echo 'selected'; ?>>Condominium</option>
                            <option value="Chalet" <?php if (isset($type) && $type == 'Chalet') echo 'selected'; ?>>Chalet</option>
                            <option value="Bungalow" <?php if (isset($type) && $type == 'Bungalow') echo 'selected'; ?>>Bungalow</option>
                            <option value="Terrace" <?php if (isset($type) && $type == 'Terrace') echo 'selected'; ?>>Terrace</option>
                            <option value="Villa" <?php if (isset($type) && $type == 'Villa') echo 'selected'; ?>>Villa</option>
                        </select>
                    </div>
                    <span class="error"><?php echo $typeErr; ?></span>
                    
                    <label for="rate">Rate</label>
                    <input type="number" id="rate" name="rate" value="<?php echo htmlspecialchars($rate ?? ''); ?>" required>
                    <span class="error"><?php echo $rateErr; ?></span>
                    
                    <label for="details">Details</label>
                    <textarea id="details" name="details" required><?php echo htmlspecialchars($details ?? ''); ?></textarea>
                    <span class="error"><?php echo $detailsErr; ?></span>
                    
                    <label for="upload">Upload House Photos</label>
                    <button type="button" id="upload" class="form_button upload_button">
                        <img src="image/uploadIcon.png" class="upload_icon"> Upload
                    </button>
                    <input type="file" id="upload_input" name="upload_input[]" multiple required style="display: none;">
                    <span class="error"><?php echo $imageErr; ?></span>
                    
                    <div id="uploaded_photos" class="uploaded_photos"></div>
                    <br>
                    <div class="form_buttons">
                        <button type="reset" class="form_button">Reset</button>
                        <button type="submit" class="form_button">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.getElementById('upload').addEventListener('click', function() {
            document.getElementById('upload_input').click();
        });

        document.getElementById('upload_input').addEventListener('change', function() {
            var uploadedPhotosContainer = document.getElementById('uploaded_photos');
            uploadedPhotosContainer.innerHTML = ''; // Clear previous images

            for (var i = 0; i < this.files.length; i++) {
                var file = this.files[i];
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('uploaded_image');
                    uploadedPhotosContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('houseForm').addEventListener('reset', function() {
            var uploadedPhotosContainer = document.getElementById('uploaded_photos');
            uploadedPhotosContainer.innerHTML = ''; // Clear images on reset
        });

        <?php if (isset($_SESSION['house_registered']) && $_SESSION['house_registered']) { ?>
            Swal.fire({
            icon: 'success',
            title: 'Registration Successful!',
            text: 'The house has been registered.',
            timer: 2000, // Display duration in milliseconds (2 seconds)
            showConfirmButton: false // Hide the "OK" button
        }).then(() => {
            // Redirect to AHouse.php
            window.location.href = 'AHouse.php';
        });
            <?php unset($_SESSION['house_registered']); ?> // Clear the session variable
        <?php } ?>
    </script>
</body>
</html>
