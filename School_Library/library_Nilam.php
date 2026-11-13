<?php
include('dblibrary.php'); // Ensure your database connection is included

// Define how many records per page
$nilamsPerPage = 15;

// Get the current page from URL, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting row for pagination
$offset = ($page - 1) * $nilamsPerPage;

// Initialize search variables
$searchColumn = isset($_GET['search_column']) ? $_GET['search_column'] : '';
$searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Default to current year

// Base SQL query
$sql = "SELECT 
            a.user_id, 
            a.id_delima, 
            a.nama_ahli, 
            a.Tingkatan, 
            a.Kelas, 
            COUNT(t.transaksi_id) AS jumlah_bacaan_bulan, 
            COUNT(t.transaksi_id) AS jumlah_bacaan_total
        FROM ahli a
        LEFT JOIN transaksi t ON a.user_id = t.user_id";

// Add search filter if a search criterion and value are provided
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'bulan_pinjam') {
        // Filter by bulan_pinjam (month of borrowing) and selected year
        $sql .= " AND MONTH(t.tarikh_pinjam) = '$searchValue' AND YEAR(t.tarikh_pinjam) = '$selectedYear'";
    } elseif ($searchColumn === 'Kelas' || $searchColumn === 'Tingkatan' || $searchColumn === 'id_delima') {
        // For Kelas, Tingkatan, and id_delima, use exact match on the ahli table
        $sql .= " AND a.$searchColumn = '$searchValue'";
    } elseif ($searchColumn === 'nama_ahli') {
        $sql .= " AND a.$searchColumn LIKE '%$searchValue%'";
    }
} else {
    // Default filter for the selected year
    $sql .= " AND YEAR(t.tarikh_pinjam) = '$selectedYear'";
}

// Group by user details to calculate jumlah_bacaan
$sql .= " GROUP BY a.user_id, a.id_delima, a.nama_ahli, a.Tingkatan, a.Kelas";

// Exclude users with zero borrowings
$sql .= " HAVING jumlah_bacaan_total > 0";

// Order by jumlah_bacaan_total (overall total) or jumlah_bacaan_bulan (monthly total)
if (!empty($searchColumn) && $searchColumn === 'bulan_pinjam') {
    $sql .= " ORDER BY jumlah_bacaan_bulan DESC";
} else {
    $sql .= " ORDER BY jumlah_bacaan_total DESC";
}

// Add pagination to the query
$sql .= " LIMIT $nilamsPerPage OFFSET $offset";


$result = $conn->query($sql);
$nilams = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Get total number of records for pagination
$totalNilamsQuery = "SELECT COUNT(DISTINCT a.user_id) AS total 
                     FROM ahli a
                     LEFT JOIN transaksi t ON a.user_id = t.user_id";

if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'bulan_pinjam') {
        $totalNilamsQuery .= " AND MONTH(t.tarikh_pinjam) = '$searchValue' AND YEAR(t.tarikh_pinjam) = '$selectedYear'";
    } elseif ($searchColumn === 'Kelas' || $searchColumn === 'Tingkatan' || $searchColumn === 'id_delima') {
        $totalNilamsQuery .= " AND a.$searchColumn = '$searchValue'";
    } elseif ($searchColumn === 'nama_ahli') {
        $totalNilamsQuery .= " AND a.$searchColumn LIKE '%$searchValue%'";
    }
} else {
    // Default filter for the selected year
    $totalNilamsQuery .= " AND YEAR(t.tarikh_pinjam) = '$selectedYear'";
}

// Exclude users with zero borrowings in the total count query
$totalNilamsQuery .= " HAVING COUNT(t.transaksi_id) > 0";

$totalNilamsResult = $conn->query($totalNilamsQuery);
$totalNilams = ($totalNilamsResult && $totalNilamsResult->num_rows > 0) ? $totalNilamsResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalNilams / $nilamsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>NILAM</title>
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
		  <a href="library_main_page.php">Laman Utama</a> | <a href="library_senarai_PSS.php">PSS</a> | <a href="library_senarai_nama.php">Nama</a> | <a href="library_Book.php">Bahan</a> | <a href="library_senarai_peminjam.php">Peminjaman</a> | <a href="library_senarai_pulang.php">Pemulangan</a> | <a href="library_History.php">Rekod</a></strong>
	  </section></p>
      <!-- Search Form -->
      <form id="searchForm" name="searchForm" method="get" action="library_Nilam.php" class="form_nilam" style="margin: 20px auto; width: 90%;">
        <table width="100%" border="0" cellpadding="10" cellspacing="0">
          <tr>
            <td width="20%" align="right"><strong>Tahun:</strong></td>
            <td width="30%">
              <select name="year" id="year" style="width: 100%;">
                <?php
                $currentYear = date('Y');
                for ($year = $currentYear; $year >= 2025; $year--) { // Adjust the range as needed
                    echo "<option value='$year' " . ($selectedYear == $year ? 'selected' : '') . ">$year</option>";
                }
                ?>
              </select>
            </td>
            <td width="20%" align="right"><strong>Cari Mengikut:</strong></td>
            <td width="30%">
              <select name="search_column" id="search_column" onchange="updateSearchInput()" style="width: 100%;">
                <option value="">-- Pilih --</option>
                <option value="id_delima" <?= $searchColumn === 'id_delima' ? 'selected' : ''; ?>>ID</option>
                <option value="nama_ahli" <?= $searchColumn === 'nama_ahli' ? 'selected' : ''; ?>>Nama</option>
                <option value="Tingkatan" <?= $searchColumn === 'Tingkatan' ? 'selected' : ''; ?>>Tingkatan</option>
                <option value="Kelas" <?= $searchColumn === 'Kelas' ? 'selected' : ''; ?>>Kelas</option>
                <option value="bulan_pinjam" <?= $searchColumn === 'bulan_pinjam' ? 'selected' : ''; ?>>Bulan Pinjam</option>
              </select>
            </td>
          </tr>
          <tr>
            <td width="20%" align="right"><strong>Nilai Carian:</strong></td>
            <td width="30%">
              <div id="search_value_container">
                <?php if ($searchColumn === 'bulan_pinjam'): ?>
                  <select name="search_value" id="search_value" style="width: 100%;">
                    <option value="">-- Pilih Bulan --</option>
                    <?php
                    $months = ["01" => "Januari", "02" => "Februari", "03" => "Mac", "04" => "April", "05" => "Mei", "06" => "Jun", "07" => "Julai", "08" => "Ogos", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Disember"];
                    foreach ($months as $key => $month) {
                        echo "<option value='$key' " . ($searchValue == $key ? 'selected' : '') . ">$month</option>";
                    }
                    ?>
                  </select>
                <?php else: ?>
                  <input type="text" name="search_value" id="search_value" value="<?= $searchValue; ?>" style="width: 100%;">
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="4" align="center">
              <input type="submit" value="Cari">
              <input type="button" value="Reset" onclick="window.location.href='library_Nilam.php';">
            </td>
          </tr>
        </table>
      </form>
      <!-- Display Nilam Records -->
      <form id="form8" name="form8" method="post" enctype="multipart/form-data">
        <table class="form_nilam" width="90%" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
          <thead> 
            <tr>
              <td colspan="15" align="center"><strong>Rekod Nilam (Tahun <?= $selectedYear; ?>)</strong></td>
            </tr>
            <tr>
              <th width="40" align="center">No</th>
              <th width="86" align="center">No ID</th>
              <th width="89" align="center">Nama</th>
              <th width="81" align="center">Tingkatan</th>
              <th width="81" align="center">Kelas</th>
              <th width="100" align="center">Jumlah Bacaan (Bulan)</th>
              <th width="100" align="center">Jumlah Bacaan (Tahun <?= $selectedYear; ?>)</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (!empty($nilams)) {
                $no = $offset + 1; // Adjust numbering based on pagination
                foreach ($nilams as $row) {
                    ?>
                    <tr>
                        <td align="center"><?php echo $no; ?></td>
                        <td align="center"><?php echo $row['id_delima']; ?></td>
                        <td align="center"><?php echo $row['nama_ahli']; ?></td>
                        <td align="center"><?php echo $row['Tingkatan']; ?></td>
                        <td align="center"><?php echo $row['Kelas']; ?></td>
                        <td align="center"><?php echo $row['jumlah_bacaan_bulan']; ?></td>
                        <td align="center"><?php echo $row['jumlah_bacaan_total']; ?></td>
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
      <div class="form_nilam" style="text-align: center;">
        <?php if ($page > 1): ?>
            <a href="?page=1&search_column=<?= $searchColumn; ?>&search_value=<?= $searchValue; ?>&year=<?= $selectedYear; ?>">⏪ First</a>
            <a href="?page=<?= $page - 1; ?>&search_column=<?= $searchColumn; ?>&search_value=<?= $searchValue; ?>&year=<?= $selectedYear; ?>">⏮ Prev</a>
        <?php endif; ?>
        <span> Page <?= $page; ?> of <?= $totalPages; ?> </span>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1; ?>&search_column=<?= $searchColumn; ?>&search_value=<?= $searchValue; ?>&year=<?= $selectedYear; ?>">Next ⏭</a>
            <a href="?page=<?= $totalPages; ?>&search_column=<?= $searchColumn; ?>&search_value=<?= $searchValue; ?>&year=<?= $selectedYear; ?>">Last ⏩</a>
        <?php endif; ?>
      </div>
      <script>
        function updateSearchInput() {
          const searchColumn = document.getElementById('search_column').value;
          const searchValueContainer = document.getElementById('search_value_container');

          if (searchColumn === 'bulan_pinjam') {
            searchValueContainer.innerHTML = `
              <select name="search_value" id="search_value" style="width: 100%;">
                <option value="">-- Pilih Bulan --</option>
                <?php
                $months = ["01" => "Januari", "02" => "Februari", "03" => "Mac", "04" => "April", "05" => "Mei", "06" => "Jun", "07" => "Julai", "08" => "Ogos", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Disember"];
                foreach ($months as $key => $month) {
                    echo "<option value='$key'>$month</option>";
                }
                ?>
              </select>
            `;
          } else if (searchColumn === 'Kelas') {
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
          }else if (searchColumn === 'Tingkatan') {
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
          }else {
            searchValueContainer.innerHTML = '<input type="text" name="search_value" id="search_value" style="width: 100%;">';
          }
        }

        // Call the function on page load to set the initial input field
        updateSearchInput();
      </script>
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
      <script src="js/jquery-3.4.1.min.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed --> 
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap-4.4.1.js"></script>
      <p><section class="PSS_section" align="center">
        <strong><a href="admin_library_logout.php">Logout</a></strong>
      </section></p>
    </main>
  </body>
</html>