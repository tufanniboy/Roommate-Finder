<?php
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../signup.html?error=invalid_request");
    exit;
}

// Get form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$age = $_POST['age'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Server-side validation
$errors = [];

// Validate name
if (empty($name)) {
    $errors[] = "Name is required";
}

// Validate email
if (empty($email)) {
    $errors[] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

// Validate phone
if (empty($phone)) {
    $errors[] = "Phone number is required";
} elseif (!preg_match("/^\d{10}$/", $phone)) {
    $errors[] = "Phone number must be 10 digits";
}

// Validate address
if (empty($address)) {
    $errors[] = "Address is required";
}

// Validate age
if (empty($age)) {
    $errors[] = "Age is required";
} elseif (!is_numeric($age) || $age < 18) {
    $errors[] = "Age must be at least 18";
}

// Validate password
if (empty($password)) {
    $errors[] = "Password is required";
} elseif (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters";
}

// Validate confirm password
if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match";
}

// If there are validation errors, redirect back to the signup page
if (!empty($errors)) {
    $error_string = implode(", ", $errors);
    header("Location: ../signup.html?error=" . urlencode($error_string));
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: ../signup.html?error=" . urlencode("Email already registered"));
    exit;
}
$stmt->close();

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, age, password) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssis", $name, $email, $phone, $address, $age, $hashed_password);

if ($stmt->execute()) {
    // Get the new user ID
    $user_id = $stmt->insert_id;
    
    // Create a session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    
    // Redirect to questionnaire page
    header("Location: ../questionnaire.html");
} else {
    header("Location: ../signup.html?error=" . urlencode("Registration failed: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>