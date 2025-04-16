<?php
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html?error=" . urlencode("Please login first"));
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../questionnaire.html?error=invalid_request");
    exit;
}

// Get form data
$location = $_POST['location'] ?? '';
$budget = $_POST['budget'] ?? '';
$roommates = $_POST['roommates'] ?? '';
$lifestyle = $_POST['lifestyle'] ?? '';

// Server-side validation
$errors = [];

// Validate location
if (empty($location)) {
    $errors[] = "Location is required";
}

// Validate budget
if (empty($budget)) {
    $errors[] = "Budget is required";
}

// Validate roommates
if (empty($roommates)) {
    $errors[] = "Number of roommates is required";
}

// Validate lifestyle
if (empty($lifestyle)) {
    $errors[] = "Lifestyle preference is required";
}

// If there are validation errors, redirect back to the questionnaire page
if (!empty($errors)) {
    $error_string = implode(", ", $errors);
    header("Location: ../questionnaire.html?error=" . urlencode($error_string));
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if preferences already exist for this user
$stmt = $conn->prepare("SELECT id FROM preferences WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing preferences
    $stmt = $conn->prepare("UPDATE preferences SET location = ?, budget = ?, roommates = ?, lifestyle = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $location, $budget, $roommates, $lifestyle, $user_id);
} else {
    // Insert new preferences
    $stmt = $conn->prepare("INSERT INTO preferences (user_id, location, budget, roommates, lifestyle) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $location, $budget, $roommates, $lifestyle);
}

if ($stmt->execute()) {
    // Redirect to matches page
    header("Location: ../matches.html");
} else {
    header("Location: ../questionnaire.html?error=" . urlencode("Failed to save preferences: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>