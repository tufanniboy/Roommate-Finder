<?php
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Set headers for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get the current user's preferences
$stmt = $conn->prepare("
    SELECT location, budget, roommates, lifestyle 
    FROM preferences 
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User preferences not found']);
    exit;
}

$user_prefs = $result->fetch_assoc();
$stmt->close();

// Find matches based on preferences
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.phone, u.age, p.location, p.budget, p.roommates, p.lifestyle
    FROM users u
    JOIN preferences p ON u.id = p.user_id
    WHERE u.id != ? 
    AND p.location = ?
    ORDER BY 
        CASE WHEN p.budget = ? THEN 3 ELSE 0 END +
        CASE WHEN p.roommates = ? THEN 2 ELSE 0 END +
        CASE WHEN p.lifestyle = ? THEN 2 ELSE 0 END DESC
    LIMIT 10
");
$stmt->bind_param("issss", $user_id, $user_prefs['location'], $user_prefs['budget'], $user_prefs['roommates'], $user_prefs['lifestyle']);
$stmt->execute();
$result = $stmt->get_result();

$matches = [];
while ($row = $result->fetch_assoc()) {
    // Calculate compatibility score (simplified version)
    $compatibility = 70; // Base score
    
    if ($row['budget'] === $user_prefs['budget']) {
        $compatibility += 10;
    }
    
    if ($row['roommates'] === $user_prefs['roommates']) {
        $compatibility += 10;
    }
    
    if ($row['lifestyle'] === $user_prefs['lifestyle']) {
        $compatibility += 10;
    }
    
    $row['compatibility'] = $compatibility;
    $matches[] = $row;
}

echo json_encode(['success' => true, 'matches' => $matches]);

$stmt->close();
$conn->close();
?>