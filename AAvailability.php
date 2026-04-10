<?php
// Fetch the logged-in agent's data
$username = $_SESSION['username'];

// Query to get the agent's houses and their rentals
$query = "
    SELECT house.house_id, house.house_availability, rental.checkin_date, rental.checkout_date
    FROM house 
    LEFT JOIN rental ON house.house_id = rental.house_id 
    WHERE house.agent_id = ? AND rental.rental_status='Accepted'
";

$stmt = $dbCon->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$currentDate = date('Y-m-d');
$houses = [];

// Organize the houses and their rentals
while ($row = $result->fetch_assoc()) {
    $houseId = $row['house_id'];
    if (!isset($houses[$houseId])) {
        $houses[$houseId] = [
            'availability' => $row['house_availability'],
            'rentals' => []
        ];
    }
    if (!is_null($row['checkin_date']) && !is_null($row['checkout_date'])) {
        $houses[$houseId]['rentals'][] = [
            'checkin_date' => $row['checkin_date'],
            'checkout_date' => $row['checkout_date']
        ];
    }
}

// Update house availability based on rental dates
foreach ($houses as $houseId => $data) {
    $isAvailable = true;

    foreach ($data['rentals'] as $rental) {
        if ($currentDate == $rental['checkin_date']) {
            // Current date is the check-in date, mark as 'Unavailable'
            $isAvailable = false;
            break;
        } elseif ($currentDate == $rental['checkout_date']) {
            // Current date is the check-out date, mark as 'Available'
            $isAvailable = true;
            break;
        } elseif ($currentDate > $rental['checkin_date'] && $currentDate < $rental['checkout_date']) {
            // Current date is within the rental period, mark as 'Unavailable'
            $isAvailable = false;
            break;
        }
    }

    $newAvailability = $isAvailable ? 'Available' : 'Unavailable';

    if ($newAvailability !== $data['availability']) {
        $updateQuery = "UPDATE house SET house_availability = ? WHERE house_id = ?";
        $updateStmt = $dbCon->prepare($updateQuery);
        $updateStmt->bind_param("ss", $newAvailability, $houseId);
        $updateStmt->execute();
    }
}

?>
