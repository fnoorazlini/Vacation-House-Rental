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
$totalHouses = 0;
$houses = [];

// Fetch house data from the database based on state if selected
$sql = "SELECT house_id, house_name, house_address, house_state, house_type, house_rate, house_image, house_details FROM house";

if (isset($_GET['state']) && !empty($_GET['state'])) {
    $state = $_GET['state'];
    $sql .= " WHERE house_state = '$state'";
}

$result = mysqli_query($dbCon, $sql);

if ($result) {
    $houses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $totalHouses = mysqli_num_rows($result); // Count total houses found
} else {
    echo "Error fetching houses: " . mysqli_error($dbCon);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Guest | Explore</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- To connect with css file-->
    <link rel="stylesheet" href="css/gExploreStyle.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

        .search-button {
            width: 30px;
            height: 30px;
            background-color: #1A8096;
            background-image: url('image/searchVector.png');
            background-size: 60%;
            background-repeat: no-repeat;
            background-position: center;
            border: none;
            cursor: pointer;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0px 4px 9px rgba(0, 0, 0, 0.25);
            margin-top: 3px;
            margin-left: 5px;
            border: #374E51;
        }

        .search-button:hover {
            transform: scale(1.13);
            background-color: #0e3a43;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        .header-container {
            width: 100%;
            overflow: hidden;
            position: relative;
            background-color: white;
            z-index: 0;
            max-height: 200px;
        }

        .header-container img {
            width: 110%;
            height: auto;
            display: block;
        }

        .Search-Section {
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translate(-50%, -50%);

            height: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            box-shadow: 7px 4px 8px rgba(0, 0, 0, 0.25);
            background: linear-gradient(180deg, rgba(255, 250.40, 250.40, 0.80) 61%, rgba(241.01, 248.69, 250.40, 0.80) 100%);
            border-radius: 17px;
            border: 3px #1A8096 solid;
        }

        .content_section {
            min-height: 550px;
            position: relative;
            align-items: center;
            flex-direction: column;
            overflow: hidden;
        }

        .location-date-container {
            display: flex;
            justify-content: space-between;
            margin-left: 10px;
        }

        .location-search,
        .datePick-search {
            display: flex;
            align-items: center;
            background-color: white;
            padding: 7px;
            border-radius: 10px;
            border: 3px #1A8096 solid;
        }

        .location-search img,
        .datePick-search img {
            height: 20px;
            width: 20px;
            margin-right: 6px;
            margin-left: 5px;
        }

        .location-search select,
        .datePick-search input {
            border: none;
            background: none;
            font-family: 'Inter', sans-serif;
            flex-grow: 1;
        }

        .datePick-search input[type="date"] {
            margin-left: 10px;
        }

        .houseDisplay-section {
            display: flex;
            overflow: hidden;
            width: 94%;
            margin: 0 auto;
            position: relative;
        }

        .houseBox-container {
            display: flex;
            transition: transform 0.5s ease;
        }

        .houseBox {
            position: relative;
            cursor: pointer;
            width: 230px;
            height: 300px;
            margin-bottom: 20px;
            background-color: #fdfdfd;
            border-radius: 12px;
            border: 3px #1A8096 transparent;
            box-shadow: 0 4px 9px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            margin-right: 20px;
            transition: transform 0.3s ease;
            margin-top: 10px;
            overflow: hidden;
        }

        .houseBox:hover {
            transform: translateY(-10px);
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
            filter: drop-shadow(1px 1px 2px rgba(244, 242, 239, 0.8));
        }

        .houseBox a {
            display: block;
            width: 100%;
            height: 100%;
            text-decoration: none;
            color: #05353d;
            position: relative;
        }

        .houseBox img {
            width: 100%;
            height: 70%;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .houseBox .text {
            padding: 8px;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            flex-direction: row;
            align-items: center;
        }

        .house_details {
            flex: 1;
            text-align: left;
            width: 120px;
        }

        .house_details h3,
        .house_details p {
            margin: 3px 0;
        }

        .price_sec {
            position: absolute;
            top: 180px;
            /* Adjust top distance */
            right: 10px;
            /* Adjust right distance */
            background-color: rgba(255, 255, 255, 0.8);
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100px;
        }

        .price_sec h3 {
            margin: 0;
            color: #1A8096;
            font-size: 14px;
            font-weight: 800;
        }

        .carousel-controls {
            position: absolute;
            top: 80%;
            width: 95%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
            margin-left: 3%;
        }

        .carousel-control {
            background-color: rgba(3, 238, 191, 0.479);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            font-size: 30px;
            cursor: pointer;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            filter: drop-shadow(2px 2px 2px rgba(0, 0, 0, 0.8));
            opacity: 0.8;
        }

        .carousel-control:hover {
            background-color: rgba(4, 240, 221, 0.932);
            color: #05353d;
            opacity: 1;
        }

        .carousel-control:disabled {
            background-color: rgba(0, 0, 0, 0.2);
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <?php include('navGuest.php'); ?>
    <div class="header-container">
        <img src="image/KLheader.png">
        <div class="Search-Section">
            <form>
                <div class="location-date-container">
                    <div class="location-search">
                        <img src="image/locationIcon.png" alt="Location Icon">
                        <form method="GET">
                            <select name="state">
                                <option value="">Select State</option>
                                <option value="Kuala Lumpur">Kuala Lumpur</option>
                                <option value="Selangor">Selangor</option>
                                <option value="Terengganu">Terengganu</option>
                                <option value="Negeri Sembilan">Negeri Sembilan</option>
                                <option value="Pulau Pinang">Pulau Pinang</option>
                            </select>
                            <input type="submit" value="" class="search-button">
                        </form>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="content_section">
        <?php if ($totalHouses > 0) : ?>
            <h4 style="font-size: 16px; font-weight:600; text-align:left; color:White; margin-left:5%;">Total Houses Display: <?php echo $totalHouses; ?></h4>
            <div class="houseDisplay-section">
                <div class="houseBox-container">
                    <?php foreach ($houses as $house) : ?>
                        <div class="houseBox">
                            <a href="GHouseDetails.php?house_id=<?php echo $house['house_id']; ?>">
                                <?php
                                $imagePaths = explode(',', $house['house_image']);
                                $firstImage = $imagePaths[0];
                                if (file_exists($firstImage)) {
                                    echo "<img src='" . $firstImage . "' alt='House Image'>";
                                } else {
                                    echo '<p>Image not found: ' . $firstImage . '</p>';
                                }
                                ?>
                                <div class="price_sec">
                                    <h3>RM <?php echo $house['house_rate']; ?><span style="font-weight: 400; font-size: 14px;">/night</span></h3>
                                </div>
                                <div class="text">
                                    <div class="house_details">
                                        <h3><?php echo $house['house_name']; ?></h3>
                                        <p>&nbsp;━━<?php echo $house['house_state']; ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p>No houses found based on the selected state.</p>
            <?php endif; ?>
            </div>
    </div>

    <div class="carousel-controls">
        <button class="carousel-control prev">&lt;</button>
        <button class="carousel-control next">&gt;</button>
    </div>

    <script>
        const houseBoxContainer = document.querySelector('.houseBox-container');
        const prevButton = document.querySelector('.prev');
        const nextButton = document.querySelector('.next');
        const houseBoxes = document.querySelectorAll('.houseBox');
        const houseBoxWidth = houseBoxes[0].offsetWidth + 20; // 20px for margin-right

        let currentIndex = 0;

        function updateCarousel() {
            houseBoxContainer.style.transform = `translateX(-${currentIndex * houseBoxWidth}px)`;
            prevButton.disabled = currentIndex === 0;
            nextButton.disabled = currentIndex === houseBoxes.length - 1;
        }

        prevButton.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });

        nextButton.addEventListener('click', () => {
            if (currentIndex < houseBoxes.length - 1) {
                currentIndex++;
                updateCarousel();
            }
        });

        updateCarousel();
    </script>
</body>

</html>