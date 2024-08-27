<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include('db.php');

// Get user ID to delete
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$email = $_SESSION['email'];

// Fetch user data
$sql = "SELECT email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    $_SESSION['message'] = "User not found.";
    header("Location: welcome.php");
    exit();
}

// Check if the logged-in user is trying to delete their own profile
if ($user['email'] !== $email) {
    $_SESSION['message'] = "You can only delete your own profile.";
    header("Location: welcome.php");
    exit();
}

// Proceed with deletion
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    // Log out the user after deletion
    session_unset();
    session_destroy();
    $_SESSION['message'] = "Your profile has been deleted. Please log in again.";
    header("Location: login.php");
    exit();
} else {
    $_SESSION['message'] = "Failed to delete user.";
    header("Location: welcome.php");
    exit();
}

// Close database connection
$conn->close();
?>
