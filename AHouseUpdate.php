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

// Get house ID from query parameter
$houseID = $_GET['house_id'];

// Fetch current house data
$sql = "SELECT * FROM house WHERE house_id = ?";
$stmt = $dbCon->prepare($sql);
$stmt->bind_param("s", $houseID);
$stmt->execute();
$result = $stmt->get_result();
$house = $result->fetch_assoc();
$stmt->close();

// Handle form submission
$nameErr = $addressErr = $stateErr = $rateErr = $imageErr = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $valid = true; // Flag to check overall form validity

    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
        $valid = false;
    } else {
        $name = $_POST['name'];
    }

    // Validate address
    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
        $valid = false;
    } else {
        $address = $_POST['address'];
    }

    // Validate state (cannot contain numbers)
    if (empty($_POST["state"])) {
        $stateErr = "State is required";
        $valid = false;
    } else {
        $state = $_POST['state'];
        if (preg_match('/[0-9]/', $state)) {
            $stateErr = "State cannot contain numbers";
            $valid = false;
        }
    }

    // Validate type
    if (empty($_POST["type"])) {
        $typeErr = "Type is required";
        $valid = false;
    } else {
        $type = $_POST['type'];
    }

    // Validate rate
    if (empty($_POST["rate"])) {
        $rateErr = "Rate is required";
        $valid = false;
    } else {
        $rate = $_POST['rate'];
        // Validate numeric value for rate
        if (!is_numeric($rate)) {
            $rateErr = "Rate must be a numeric value";
            $valid = false;
        }
    }

    // Validate details
    if (empty($_POST["details"])) {
        $details = "";
    } else {
        $details = $_POST['details'];
    }

    // Handle file uploads
    $uploaded_files = [];
    $target_dir = "upload/";
    $files = $_FILES['upload_input'];

    if (!empty($files['name'][0])) {
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
                $valid = false;
            }

            // Check if file already exists
            if (file_exists($target_file)) {
                $uploadOk = 0;
                $imageErr = "Sorry, file already exists.";
                $valid = false;
            }

            // Check file size
            if ($files["size"][$i] > 5000000) {
                $uploadOk = 0;
                $imageErr = "Sorry, your file is too large.";
                $valid = false;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $uploadOk = 0;
                $imageErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $valid = false;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $imageErr = "Sorry, your file was not uploaded.";
            } else {
                if (move_uploaded_file($files["tmp_name"][$i], $target_file)) {
                    $uploaded_files[] = $target_file;
                } else {
                    $imageErr = "Sorry, there was an error uploading your file.";
                    $valid = false;
                }
            }
        }

        if (count($uploaded_files) > 0) {
            $uploaded_files_str = implode(',', $uploaded_files);
        } else {
            // If no new images uploaded, keep the existing images
            $uploaded_files_str = $house['house_image'];
        }
    } else {
        // No new images selected, keep the existing images
        $uploaded_files_str = $house['house_image'];
    }

    // Update house data in the database if form is valid
    if ($valid) {
        $sql = "UPDATE house SET house_name = ?, house_address = ?, house_state = ?, house_type = ?, house_rate = ?, house_availability = ?, house_details = ?, house_image = ?, agent_id = ? WHERE house_id = ?";
        $stmt = $dbCon->prepare($sql);
        $stmt->bind_param("ssssisssss", $name, $address, $state, $type, $rate, $house['house_availability'], $details, $uploaded_files_str, $agent_id, $houseID);

        if ($stmt->execute()) {
            $_SESSION['house_updated'] = true; // Set session variable for success message
            header('Location: AHouseView.php?house_id=' . urlencode($houseID));
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$dbCon->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Agent | Update House</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS Files -->
    <link rel="stylesheet" href="css/aHouseRegister.css">
    <link rel="stylesheet" href="css/aOuterStructure.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        .error {
            color: red;
        }
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
        <h2>House Update</h2>
    </div>

    <div class="content_section">
        <div class="registration_container">
            <div class="container_header">
                <h2>HOUSE UPDATE</h2>
                <a href="AHouseView.php?house_id=<?php echo htmlspecialchars($houseID); ?>" class="close_button">X</a>
            </div>
            <div class="form_container">
                <form id="houseForm" method="POST" enctype="multipart/form-data">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($house['house_name']); ?>">
                    <span class="error"><?php echo $nameErr; ?></span>
                    
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($house['house_address']); ?>">
                    <span class="error"><?php echo $addressErr; ?></span>
                    
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($house['house_state']); ?>">
                    <span class="error"><?php echo $stateErr; ?></span>
                    
                    <label for="type">Type</label>
                    <div class="custom_select">
                        <select id="type" name="type">
                            <option value="Apartment" <?php echo ($house['house_type'] == 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                            <option value="Condominium" <?php echo ($house['house_type'] == 'Condominium') ? 'selected' : ''; ?>>Condominium</option>
                            <option value="Chalet" <?php echo ($house['house_type'] == 'Chalet') ? 'selected' : ''; ?>>Chalet</option>
                            <option value="Bungalow" <?php echo ($house['house_type'] == 'Bungalow') ? 'selected' : ''; ?>>Bungalow</option>
                            <option value="Terrace" <?php echo ($house['house_type'] == 'Terrace') ? 'selected' : ''; ?>>Terrace</option>
                            <option value="Villa" <?php echo ($house['house_type'] == 'Villa') ? 'selected' : ''; ?>>Villa</option>
                        </select>
                    </div>
                    
                    <label for="rate">Rate</label>
                    <input type="text" id="rate" name="rate" value="<?php echo htmlspecialchars($house['house_rate']); ?>">
                    <span class="error"><?php echo $rateErr; ?></span>
                    
                    <label for="details">Details</label>
                    <textarea id="details" name="details"><?php echo htmlspecialchars($house['house_details']); ?></textarea>
                    
                    <label for="upload">Upload House Photos</label>
                    <button type="button" id="upload" class="form_button upload_button">
                        <img src="image/uploadIcon.png" class="upload_icon"> Upload
                    </button>
                    <input type="file" id="upload_input" name="upload_input[]" multiple style="display: none;">
                    <span class="error"><?php echo $imageErr; ?></span>
                    
                    <div id="uploaded_photos" class="uploaded_photos">
                        <?php
                        $images = explode(',', $house['house_image']);
                        foreach ($images as $image) {
                            echo '<img src="' . htmlspecialchars($image) . '" class="uploaded_image">';
                        }
                        ?>
                    </div>
                    <br>
                    <div class="form_buttons">
                        <button type="submit" class="form_button" id="updateButton">Update</button>
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

        document.getElementById('updateButton').addEventListener('click', function(event) {
            // Check form validity before showing confirmation
            var form = document.getElementById('houseForm');
            if (form.checkValidity()) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Changes will be updated.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('houseForm').submit();
                    }
                });
            }
            event.preventDefault();
        });
    </script>
</body>
</html>
