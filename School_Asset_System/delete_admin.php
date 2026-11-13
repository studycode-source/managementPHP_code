<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'school_asset_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected IDs from the form
    if (isset($_POST['delete_ids']) && !empty($_POST['delete_ids'])) {
        $delete_ids = $_POST['delete_ids'];

        // Prepare IDs for deletion
        $delete_ids_str = implode(",", array_map('intval', $delete_ids)); // Convert array to comma-separated string of integers

        // SQL query to delete the selected admin  members
        $sql_delete = "DELETE FROM user WHERE user_id IN ($delete_ids_str)";

        // Execute the query
        if ($conn->query($sql_delete) === TRUE) {
            echo "Selected admin  members deleted successfully.";
        } else {
            echo "Error deleting records: " . $conn->error;
        }
    } else {
        echo "No admin selected for deletion.";
    }
}

// Close the database connection
$conn->close();

// Redirect back to the admin list page (or wherever you'd like to go after deletion)
header("Location: admin_main_Home.php"); // Change this if your file path is different
exit();
?>
