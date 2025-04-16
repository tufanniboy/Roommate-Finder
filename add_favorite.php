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

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (empty($data['match_id'])) {
    echo json_encode(['success' => false, 'message' => 'Match ID is required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$match_id = $data['match_id'];

// Check if already favorited
$stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND match_id = ?");
$stmt->bind_param("ii", $user_id, $match_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already added to favorites']);
    exit;
}
$stmt->close();

// Add to favorites
$stmt = $conn->prepare("INSERT INTO favorites (user_id, match_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $match_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Added to favorites successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add to favorites: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>