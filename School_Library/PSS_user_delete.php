<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'school_library_system');

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

        // SQL query to delete the selected user  members
        $sql_delete = "DELETE FROM ahli WHERE user_id IN ($delete_ids_str)";

        // Execute the query
        if ($conn->query($sql_delete) === TRUE) {
            echo "Nama yang dipilih berjaya dipadam";
        } else {
            echo "Ralat memadam rekod. " . $conn->error;
        }
    } else {
        echo "Tiada Nama yang dipilih untuk dipadam";
    }
}

// Close the database connection
$conn->close();

// Redirect back to the main page after deletion
header("Location: PSS_senarai_nama.php");
exit();
?>
