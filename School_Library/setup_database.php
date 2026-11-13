<?php
$host = 'localhost'; // MySQL server hostname
$user = 'root';      // MySQL username (default for XAMPP)
$password = '';      // MySQL password (default for XAMPP is empty)
$database = 'school_library_system'; // Your database name in MySQL

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql)) {
    echo "Database created successfully.\n";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select database
$conn->select_db($database);

// Import SQL file
$sqlFile = file_get_contents('school_library_system.sql'); // Path to your .sql file
if ($conn->multi_query($sqlFile)) {
    echo "Database setup completed successfully.\n";
} else {
    echo "Error setting up database: " . $conn->error;
}

$conn->close();
?>