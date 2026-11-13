<?php
include('dblibrary.php'); // Ensure your database connection is included

// Fetch Kelas options from the senarai_rak table
$sql = "SELECT nama_rak FROM senarai_rak";
$result = $conn->query($sql);
$rakOptions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rakOptions[] = $row['nama_rak'];
    }
}

// Return the options as JSON
echo json_encode($rakOptions);
?>