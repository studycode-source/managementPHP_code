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

        // SQL query to delete the selected rak  members
        $sql_delete = "DELETE FROM senarai_rak WHERE rak_id IN ($delete_ids_str)";

        // Execute the query
        if ($conn->query($sql_delete) === TRUE) {
            echo '<script>alert("Maklumat Rak Berjaya dipadam")</script>';
        } else {
            echo "Ralat memadam rekod. " . $conn->error;
        }
    } else {
        echo "Tiada rak yang dipilih untuk dipadam";
    }
}

// Close the database connection
$conn->close();

// Redirect back to the main page after deletion
header("Location: add_rak.php");
exit();
?>
