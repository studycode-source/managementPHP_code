<?php
include('dblibrary.php'); // Ensure your database connection is included

// Fetch Kelas options from the senarai_kelas table
$sql = "SELECT value FROM senarai_kelas";
$result = $conn->query($sql);
$kelasOptions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $kelasOptions[] = $row['value'];
    }
}

// Return the options as JSON
echo json_encode($kelasOptions);
?>