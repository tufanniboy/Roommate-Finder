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

// Get favorites with user details
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.phone, u.age, p.location, p.budget, p.roommates, p.lifestyle
    FROM favorites f
    JOIN users u ON f.match_id = u.id
    JOIN preferences p ON u.id = p.user_id
    WHERE f.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$favorites = [];
while ($row = $result->fetch_assoc()) {
    $favorites[] = $row;
}

echo json_encode(['success' => true, 'favorites' => $favorites]);

$stmt->close();
$conn->close();
?>