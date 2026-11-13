<?php
include('dblibrary.php'); // Ensure your database connection is included

// Define how many historys per page
$historysPerPage = 15;

// Get the current page from URL, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting row for pagination
$offset = ($page - 1) * $historysPerPage;

// Initialize search variables
$searchColumn = isset($_GET['search_column']) ? $_GET['search_column'] : '';
$searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';

// Base SQL query
$sql = "SELECT 
            b.book_id, b.no_perolehan, b.tajuk, b.kod_buku, 
            a.user_id, a.id_delima, a.nama_ahli, a.Tingkatan, a.Kelas, a.status,
            t.tarikh_pinjam, t.masa_meminjam, t.tarikh_pulang, t.masa_pemulangan, t.jumlah_hari_transaksi, t.jumlah_denda, t.status_transaksi, t.bulan_pinjam, t.bulan_pulang
        FROM transaksi t
        INNER JOIN buku b ON t.book_id = b.book_id
        INNER JOIN ahli a ON t.user_id = a.user_id"; // Corrected join conditions

// Add search filter if a search criterion and value are provided
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'Kelas' || $searchColumn === 'Tingkatan' || $searchColumn === 'id_delima') {
        // For Kelas, Tingkatan, and id_delima, use exact match on the ahli table
        $sql .= " AND a.$searchColumn = '$searchValue'";
    } elseif ($searchColumn === 'nama_ahli') {
        $sql .= " AND a.$searchColumn LIKE '%$searchValue%'";
		
    } elseif ($searchColumn === 'tarikh_pinjam' || $searchColumn === 'tarikh_pulang') { 
        $sql .= " AND DATE(t.$searchColumn) = '$searchValue'";
		
    }else {
        // For other columns (e.g., no_perolehan, tajuk, kod_buku), use a LIKE query for partial matches on the buku table
        $sql .= " AND b.$searchColumn LIKE '%$searchValue%'";
    }
}

$sql .= " ORDER BY t.tarikh_pinjam DESC, t.tarikh_pulang DESC";


// Add pagination to the query
$sql .= " LIMIT $historysPerPage OFFSET $offset";

// Debug: Print the SQL query


$result = $conn->query($sql);
$historys = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Get total number of historys for pagination
$totalHistorysQuery = "SELECT COUNT(*) AS total FROM transaksi t
                      INNER JOIN buku b ON t.book_id = b.book_id
                      INNER JOIN ahli a ON t.user_id = a.user_id"; // Corrected join conditions
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'Kelas' || $searchColumn === 'Tingkatan' || $searchColumn === 'id_delima') {
        // For Kelas, Tingkatan, and id_delima, use exact match on the ahli table
        $totalHistorysQuery .= " AND a.$searchColumn = '$searchValue'";
    } elseif ($searchColumn === 'nama_ahli') {
        $totalHistorysQuery .= " AND a.$searchColumn LIKE '%$searchValue%'";
		
    } elseif ($searchColumn === 'tarikh_pinjam' || $searchColumn === 'tarikh_pulang') {
        $totalHistorysQuery .= " AND DATE(t.$searchColumn) = '$searchValue'";
		
    }else {
        // For other columns (e.g., no_perolehan, tajuk, kod_buku), use a LIKE query for partial matches on the buku table
        $totalHistorysQuery .= " AND b.$searchColumn LIKE '%$searchValue%'";
    }
}
// Debug: Print the total history query


$totalHistorysResult = $conn->query($totalHistorysQuery);
$totalHistorys = $totalHistorysResult->fetch_assoc()['total'];
$totalPages = ceil($totalHistorys / $historysPerPage);
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    
    <title>SEJARAH PEMINJAMAN DAN PEMULANGAN</title>
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
		 
<form id="searchForm" name="searchForm" method="get" action="PSS_History.php" class="form_rekod" style="margin: 20px auto; width: 90%;">
        <table width="100%" border="0" cellpadding="10" cellspacing="0">
          <tr>
            <td width="20%" align="right"><strong>Cari Mengikut:</strong></td>
            <td width="30%">
              <select name="search_column" id="search_column" onchange="updateSearchInput()" style="width: 100%;">
                <option value="">-- Pilih --</option>
                <option value="no_perolehan" <?= $searchColumn === 'no_perolehan' ? 'selected' : ''; ?>>No Perolehan</option>
                <option value="tajuk" <?= $searchColumn === 'tajuk' ? 'selected' : ''; ?>>Bahan/Tajuk</option>
                <option value="kod_buku" <?= $searchColumn === 'kod_buku' ? 'selected' : ''; ?>>Kod Bahan</option>
                <option value="id_delima" <?= $searchColumn === 'id_delima' ? 'selected' : ''; ?>>ID</option>
                <option value="nama_ahli" <?= $searchColumn === 'nama_ahli' ? 'selected' : ''; ?>>Nama</option>
                <option value="Tingkatan" <?= $searchColumn === 'Tingkatan' ? 'selected' : ''; ?>>Tingkatan</option>
                <option value="Kelas" <?= $searchColumn === 'Kelas' ? 'selected' : ''; ?>>Kelas</option>
				<option value="tarikh_pinjam" <?= $searchColumn === 'tarikh_pinjam' ? 'selected' : ''; ?>>Tarikh Pinjam</option>
    			<option value="tarikh_pulang" <?= $searchColumn === 'tarikh_pulang' ? 'selected' : ''; ?>>Tarikh Pulang</option>
				
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
              <input type="button" value="Reset" onclick="window.location.href='PSS_History.php';">
            </td>
			  <td align="center" margin-top="10px">
                    <a href="PSS_Nilam.php" class="daftar-bg daftar-button">Rekod Nilam</a>
                </td>
          </tr>
        </table>
      </form>
      <!-- Display History -->
      <form id="form8" name="form8" method="post" enctype="multipart/form-data">
        <table class="form_rekod" width="90%" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
          <thead> 
            <tr>
              <td colspan="15" align="center"><strong>Rekod Peminjaman dan Pemulangan</strong></td>
            </tr>
            <tr>
              <th width="40" align="center">No</th>
              <th width="86" align="center">No ID</th>
              <th width="89" align="center">Nama</th>
              <th width="81" align="center">Tingkatan</th>
              <th width="81" align="center">Kelas</th>
              <th width="89" align="center">No Perolehan</th>
              <th width="89" align="center">Bahan/Tajuk Bahan</th>
              <th width="81" align="center">Kod Bahan</th>
              <th width="70" align="center">Tarikh Meminjam</th>
              <th width="60" align="center">Masa Meminjam</th>
			  
              <th width="85" align="center">Bilangan Hari Bahan Dipinjam</th>
              <th width="70" align="center">Tarikh Pemulangan</th>
              <th width="60" align="center">Masa Pemulangan</th>
		      
              <th width="80" align="center">Status Pinjaman Bahan</th>
              <th width="81" align="center">Jumlah Denda</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (!empty($historys)) {
                $no = $offset + 1; // Adjust numbering based on pagination
                foreach ($historys as $row) {
                    ?>
                    <tr>
                        <td align="center"><?php echo $no; ?></td>
                        <td align="center"><?php echo $row['id_delima']; ?></td>
                        <td align="center"><?php echo $row['nama_ahli']; ?></td>
                        <td align="center"><?php echo $row['Tingkatan']; ?></td>
                        <td align="center"><?php echo $row['Kelas']; ?></td>
                        <td align="center"><?php echo $row['no_perolehan']; ?></td>
                        <td align="center"><?php echo $row['tajuk']; ?></td>
                        <td align="center"><?php echo $row['kod_buku']; ?></td>
                        <td align="center"><?php echo $row['tarikh_pinjam']; ?></td>
                        <td align="center"><?php echo $row['masa_meminjam']; ?></td>
						
                        <td align="center"><?php echo $row['jumlah_hari_transaksi']; ?></td>
                        <td align="center"><?php echo $row['tarikh_pulang']; ?></td>
                        <td align="center"><?php echo $row['masa_pemulangan']; ?></td>
						
                        <td align="center"><?php echo $row['status_transaksi']; ?></td>
                        <td align="center">RM<?php echo $row['jumlah_denda']; ?></td>
                    </tr>
                    <?php
                    $no++;
                }
            } else {
                ?>
                <tr><td colspan="15" align="center">Tiada rekod dijumpai</td></tr>
                <?php
            }
            ?>
          </tbody>
        </table>
      </form>
	        <br>

      <!-- Pagination -->
      <div class="form_rekod" style="text-align: center;">
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
          } else if (searchColumn === 'tarikh_pinjam' || searchColumn === 'tarikh_pulang') {
        // Display a date input for tarikh_pinjam and tarikh_pulang
        searchValueContainer.innerHTML = '<input type="date" name="search_value" id="search_value" style="width: 100%;">';
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




		 
		 


