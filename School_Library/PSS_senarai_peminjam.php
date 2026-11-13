<?php
include('dblibrary.php'); // Ensure your database connection is included

// Define how many borrows per page
$borrowsPerPage = 15;

// Get the current page from URL, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting row for pagination
$offset = ($page - 1) * $borrowsPerPage;

// Initialize search variables
$searchColumn = isset($_GET['search_column']) ? $_GET['search_column'] : '';
$searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';

// Base SQL query
$sql = "SELECT 
            b.book_id, b.no_perolehan, b.tajuk, b.kod_buku, b.Jenis_buku, b.jumlah_hari_pinjaman,
            a.user_id, a.id_delima, a.nama_ahli, a.jenis_ahli, a.Tingkatan, a.Kelas, a.status,
            t.tarikh_pinjam, t.masa_meminjam, t.status_transaksi, t.tarikh_perlu_pulang
        FROM transaksi t
        INNER JOIN buku b ON t.book_id = b.book_id
        INNER JOIN ahli a ON t.user_id = a.user_id";

// Add search filter if a search criterion and value are provided
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'Kelas' || $searchColumn === 'jenis_ahli' || $searchColumn === 'Tingkatan' || $searchColumn === 'Jenis_buku' || $searchColumn === 'id_delima') {
        // For Kelas, jenis_ahli, Tingkatan, and Jenis_buku, use exact match
        $sql .= " AND $searchColumn = '$searchValue'";
    } elseif ($searchColumn === 'nama_ahli') {
        // For nama_ahli, use a LIKE query for partial matches
        $sql .= " AND a.$searchColumn LIKE '%$searchValue%'";
    }else {
        // For other columns, use a LIKE query for partial matches
        $sql .= " AND $searchColumn LIKE '%$searchValue%'";
    }
}

// Add ORDER BY clause to sort by tarikh_pinjam in descending order (latest borrows first)
$sql .= " ORDER BY t.tarikh_pinjam AND t.masa_meminjam DESC";

// Add pagination to the query
$sql .= " LIMIT $borrowsPerPage OFFSET $offset";

$result = $conn->query($sql);
$borrows = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Get total number of borrows for pagination
$totalBorrowsQuery = "SELECT COUNT(*) AS total FROM transaksi t
                      INNER JOIN buku b ON t.book_id = b.book_id
                      INNER JOIN ahli a ON t.user_id = a.user_id";
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'Kelas' || $searchColumn === 'jenis_ahli' || $searchColumn === 'Tingkatan' || $searchColumn === 'id_delima') {
        // For Kelas, jenis_ahli, and Tingkatan, use exact match on the ahli table
        $totalBorrowsQuery .= " WHERE a.$searchColumn = '$searchValue'";
    } elseif ($searchColumn === 'Jenis_buku') {
        // For Jenis_buku, use exact match on the buku table
        $totalBorrowsQuery .= " WHERE b.$searchColumn = '$searchValue'";
    } elseif ($searchColumn === 'nama_ahli') {
        // For nama_ahli, use a LIKE query for partial matches
        $totalBorrowsQuery .= " AND a.$searchColumn LIKE '%$searchValue%'";
    }else {
        // For other columns, use a LIKE query for partial matches
        $totalBorrowsQuery .= " WHERE b.$searchColumn LIKE '%$searchValue%'";
    }
}
$totalBorrowsResult = $conn->query($totalBorrowsQuery);
$totalBorrows = $totalBorrowsResult->fetch_assoc()['total'];
$totalPages = ceil($totalBorrows / $borrowsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>SENARAI PEMINJAMAN</title>
    <link rel="stylesheet" type="text/css" href="config/css/PSS.css">
    <link href="css/bootstrap-4.4.1.css" rel="stylesheet">
  </head>
  <body>
    <div class="bg"></div>
    <main class="menu_content">
      <header class="PSS_header">
        <h1 align="center"><strong>Laman Utama Pusat Sumber SMK Dato'Onn</strong></h1>
      </header>
      <p>
	  <section class="PSS_section"><strong>
		  <a href="library_pss_page.php">Laman Utama</a> | <a href="PSS_list.php">PSS</a> | <a href="PSS_senarai_nama.php">Nama</a> | <a href="PSS_Book.php">Bahan</a> | <a href="PSS_senarai_peminjam.php">Peminjaman</a> | <a href="PSS_senarai_pulang.php">Pemulangan</a> | <a href="PSS_History.php">Rekod</a></strong>
	  </section></p>
      <!-- Search Form -->
      <form id="searchForm" name="searchForm" method="get" action="PSS_senarai_peminjam.php" class="form_pinjam" style="margin: 20px auto; width: 90%;">
        <table width="100%" border="0" cellpadding="10" cellspacing="0">
          <tr>
            <td width="20%" align="right"><strong>Cari Mengikut:</strong></td>
            <td width="30%">
              <select name="search_column" id="search_column" onchange="updateSearchInput()" style="width: 100%;">
                <option value="">-- Pilih --</option>
                <option value="no_perolehan" <?= $searchColumn === 'no_perolehan' ? 'selected' : ''; ?>>No Perolehan</option>
                <option value="tajuk" <?= $searchColumn === 'tajuk' ? 'selected' : ''; ?>>Bahan/Tajuk</option>
                <option value="kod_buku" <?= $searchColumn === 'kod_buku' ? 'selected' : ''; ?>>Kod Bahan</option>
				<option value="Jenis_buku" <?= $searchColumn === 'Jenis_buku' ? 'selected' : ''; ?>>Jenis Bahan</option>
                <option value="id_delima" <?= $searchColumn === 'id_delima' ? 'selected' : ''; ?>>ID</option>
                <option value="nama_ahli" <?= $searchColumn === 'nama_ahli' ? 'selected' : ''; ?>>Nama</option>
				<option value="jenis_ahli" <?= $searchColumn === 'jenis_ahli' ? 'selected' : ''; ?>>Jenis Ahli</option>
                <option value="Tingkatan" <?= $searchColumn === 'Tingkatan' ? 'selected' : ''; ?>>Tingkatan</option>
				<option value="Kelas" <?= $searchColumn === 'Kelas' ? 'selected' : ''; ?>>Kelas</option>
              </select>
            </td>
            <td width="20%" align="right"><strong>Nilai Carian:</strong></td>
            <td width="30%">
              <div id="search_value_container">
                <input type="text" name="search_value" id="search_value" value="<?= $searchValue; ?>" style="width: 100%;">
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="4" align="center">
              <input type="submit" value="Cari">
              <input type="button" value="Reset" onclick="window.location.href='PSS_senarai_peminjam.php';">
            </td>
			  
          </tr>
        </table>
      </form>
      <!-- Display Borrows -->
      <form id="form7" name="form7" method="post" action="PSS_Booking.php">
        <table class="form_pinjam" width="98%" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
          <thead>
            <tr>
              <td colspan="20" align="center"><strong>Senarai Maklumat Peminjam</strong></td>
            </tr>
            <tr>
              <th width="40" align="center">No</th>
              <th width="80" align="center">No Perolehan</th>
              <th width="95" align="center">Bahan/Tajuk Buku</th>
              <th width="81" align="center">Kod Bahan</th>
              <th width="86" align="center">Jenis Bahan</th>
              <th width="70" align="center">Jumlah Hari Boleh Pinjam</th>
              <th width="75" align="center">ID</th>
              <th width="100" align="center">Nama</th>
              <th width="83" align="center">Jenis Ahli</th>
              <th width="86" align="center">Tingkatan</th>
              <th width="85" align="center">Kelas</th>
              <th width="70" align="center">Status Pinjaman</th>
              <th width="60" align="center">Tarikh Meminjam</th>
              <th width="70" align="center">Masa Meminjam</th>
			  <th width="70" align="center">Tarikh Perlu Pulang</th>
              <th width="70" align="center"></th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (!empty($borrows)) {
                $no = $offset + 1; // Adjust numbering based on pagination
                foreach ($borrows as $row) {
                    ?>
                    <tr>
                        <td align="center"><?php echo $no; ?></td>
                        <td align="center"><?php echo $row['no_perolehan']; ?></td>
                        <td align="center"><?php echo $row['tajuk']; ?></td>
                        <td align="center"><?php echo $row['kod_buku']; ?></td>
                        <td align="center"><?php echo $row['Jenis_buku']; ?></td>
                        <td align="center"><?php echo $row['jumlah_hari_pinjaman']; ?></td>
                        <td align="center"><?php echo $row['id_delima']; ?></td>
                        <td align="center"><?php echo $row['nama_ahli']; ?></td>
                        <td align="center"><?php echo $row['jenis_ahli']; ?></td>
                        <td align="center"><?php echo $row['Tingkatan']; ?></td>
                        <td align="center"><?php echo $row['Kelas']; ?></td>
                        <td align="center"><?php echo $row['status_transaksi']; ?></td>
                        <td align="center"><?php echo $row['tarikh_pinjam']; ?></td>
                        <td align="center"><?php echo $row['masa_meminjam']; ?></td>
						<td align="center"><?php echo $row['tarikh_perlu_pulang']; ?></td>
                        
						<td align="center">
						<?php if($row['status_transaksi'] == "Sedang Meminjam") { ?>
							<a href="PSS_return.php?no_perolehan=<?php echo $row['no_perolehan']; ?>&id_delima=<?php echo $row['id_delima']; ?>" class="Pinjam-bg Pinjam-button">Pulang</a>
						<?php } ?>
					</td>
                    </tr>
                    <?php
                    $no++;
                }
            } else {
                ?>
                <tr><td colspan="20" align="center">Tiada buku dijumpai</td></tr>
                <?php
            }
            ?>
          </tbody>
        </table>
      </form>
      <br>
      <!-- Pagination -->
      <div class="form_pinjam" style="text-align: center;">
        <?php if ($page > 1): ?>
            <a href="?page=1&search_column=<?= $searchColumn; ?>&search_value=<?= $searchValue; ?>">⏪ First</a>
            <a href="?page=<?= $page - 1; ?>&search_column=<?= $searchColumn; ?>&search_value=<?= $searchValue; ?>">⏮ Prev</a>
        <?php endif; ?>
        <span> Page <?= $page; ?> of <?= $totalPages; ?> </span>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1; ?>&search_column=<?= $searchColumn; ?>&search_value=<?= $searchValue; ?>">Next ⏭</a>
            <a href="?page=<?= $totalPages; ?>&search_column=<?= $searchColumn; ?>&search_value=<?= $searchValue; ?>">Last ⏩</a>
        <?php endif; ?>
      </div>
      <script>
        function updateSearchInput() {
          const searchColumn = document.getElementById('search_column').value;
          const searchValueContainer = document.getElementById('search_value_container');

          if (searchColumn === 'Kelas') {
    // Fetch Kelas options from the server
    fetch('get_kelas_options.php') // Create a new PHP file to fetch Kelas options
      .then(response => response.json())
      .then(data => {
        // Create the dropdown for Kelas
        let dropdownHTML = '<select name="search_value" id="search_value" style="width: 100%;">';
        dropdownHTML += '<option value="">-- Pilih Kelas --</option>';
        data.forEach(kelas => {
          dropdownHTML += `<option value="${kelas}">${kelas}</option>`;
        });
        dropdownHTML += '</select>';
        searchValueContainer.innerHTML = dropdownHTML;
      })
      .catch(error => console.error('Error fetching Kelas options:', error));
  } else if (searchColumn === 'jenis_ahli') {
    searchValueContainer.innerHTML = `
      <select name="search_value" id="search_value" style="width: 100%;">
        <option value="">-- Pilih Jenis Ahli --</option>
        <?php
        $j_ahliOptions = ["PENGAWAS PSS", "GURU", "PELAJAR", "STAF SOKONGAN"];
        foreach ($j_ahliOptions as $j_ahli) {
            echo '<option value="' . $j_ahli . '">' . $j_ahli . '</option>';
        }
        ?>
      </select>
    `;
  } else if (searchColumn === 'Tingkatan') {
    searchValueContainer.innerHTML = `
      <select name="search_value" id="search_value" style="width: 100%;">
        <option value="">-- Pilih Tingkatan --</option>
        <?php
        $tingkatanOptions = ["Kelas Khas", "1", "2", "3", "4", "5", "6"];
        foreach ($tingkatanOptions as $tingkatan) {
            echo '<option value="' . $tingkatan . '">' . $tingkatan . '</option>';
        }
        ?>
      </select>
    `;
  }else if (searchColumn === 'Jenis_buku') {
    // Fetch jenis bahan options from the server
    fetch('get_jenis_options.php') // Create a new PHP file to fetch jenis bahan options
      .then(response => response.json())
      .then(data => {
        // Create the dropdown for Rak
        let dropdownHTML = '<select name="search_value" id="search_value" style="width: 100%;">';
        dropdownHTML += '<option value="">-- Pilih Jenis bahan --</option>';
        data.forEach(book_type => {
          dropdownHTML += `<option value="${book_type}">${book_type}</option>`;
        });
        dropdownHTML += '</select>';
        searchValueContainer.innerHTML = dropdownHTML;
      })
      .catch(error => console.error('Error fetching jenis bahan options:', error));
  }else {
            searchValueContainer.innerHTML = '<input type="text" name="search_value" id="search_value" style="width: 100%;">';
          }
        }

        // Call the function on page load to set the initial input field
        updateSearchInput();
      </script>
      <p><section class="PSS_section" align="center">
       <strong><a href="pss_library_logout.php">Logout</a></strong>
   </section></p>
    </main>
  </body>
</html>