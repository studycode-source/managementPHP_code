<?php
include('dblibrary.php'); // Ensure your database connection is included

// Fetch existing jenis_buku options
$sql = "SELECT jenis_bahan  FROM senarai_jenis_buku";
$result = $conn->query($sql);
$jenis_bukuOptions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jenis_bukuOptions[] = $row['jenis_bahan'];
    }
}


// Return the options as JSON
echo json_encode($jenis_bukuOptions);
?>