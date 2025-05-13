<?php
session_start();
include 'db.php'; // Ensure this includes database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate data
if (!isset($data['name'], $data['address_line1'], $data['city'], $data['state'], $data['country'], $data['postal_code'])) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$user_id = $_SESSION['user_id']; // Get user ID from session
$name = htmlspecialchars($data['name']);
$address_line1 = htmlspecialchars($data['address_line1']);
$address_line2 = isset($data['address_line2']) ? htmlspecialchars($data['address_line2']) : ''; // Optional
$city = htmlspecialchars($data['city']);
$state = htmlspecialchars($data['state']);
$country = htmlspecialchars($data['country']);
$postal_code = htmlspecialchars($data['postal_code']);

// Log the data for debugging
var_dump($user_id, $name, $address_line1, $address_line2, $city, $state, $country, $postal_code);

// Check if the user already has an address
$sql_check = "SELECT id FROM user_addresses WHERE user_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update existing address
    $sql = "UPDATE user_addresses SET name=?, address_line1=?, address_line2=?, city=?, state=?, country=?, postal_code=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $name, $address_line1, $address_line2, $city, $state, $country, $postal_code, $user_id);
    
} else {
    // Insert new address (ID will auto-increment, user_id is provided from session)
    $sql = "INSERT INTO user_addresses (user_id, name, address_line1, address_line2, city, state, country, postal_code) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss", $user_id, $name, $address_line1, $address_line2, $city, $state, $country, $postal_code);
}

// Execute and check result
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $stmt->error]);
}
?>
