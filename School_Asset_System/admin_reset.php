<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'school_asset_management');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get admin ID from the session
$admin_id = $_SESSION['admin_id'];

// Fetch admin details for logged-in user
$sql = "SELECT * FROM user WHERE user_id = '$admin_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    // Store admin data back to the session or redirect to the profile page
    $_SESSION['admin_data'] = $admin; // This can be used to reload the profile
    header("Location: admin_main_Home.php?id=" . $admin['user_id']);
} else {
    echo "Admin not found.";
}

// Close connection
$conn->close();
?>
