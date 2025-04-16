<?php
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.html?error=invalid_request");
    exit;
}

// Get form data
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Server-side validation
$errors = [];

// Validate email
if (empty($email)) {
    $errors[] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

// Validate password
if (empty($password)) {
    $errors[] = "Password is required";
}

// If there are validation errors, redirect back to the login page
if (!empty($errors)) {
    $error_string = implode(", ", $errors);
    header("Location: ../login.html?error=" . urlencode($error_string));
    exit;
}

// Prepare SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../login.html?error=" . urlencode("Invalid email or password"));
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (password_verify($password, $user['password'])) {
    // Password is correct, start a session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    
    // Check if user has completed the questionnaire
    $stmt = $conn->prepare("SELECT id FROM preferences WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $pref_result = $stmt->get_result();
    
    if ($pref_result->num_rows > 0) {
        // User has completed the questionnaire, redirect to matches
        header("Location: ../matches.html");
    } else {
        // User has not completed the questionnaire, redirect to questionnaire
        header("Location: ../questionnaire.html");
    }
} else {
    header("Location: ../login.html?error=" . urlencode("Invalid email or password"));
}

$stmt->close();
$conn->close();
?>