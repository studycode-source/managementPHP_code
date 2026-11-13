<?php
include('dblibrary.php'); // Ensure your database connection is included

// Define how many users per page
$usersPerPage = 50;

// Get the current page from URL, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting row for pagination
$offset = ($page - 1) * $usersPerPage;

// Initialize search variables
$searchColumn = isset($_GET['search_column']) ? $_GET['search_column'] : '';
$searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';

// Base SQL query
$sql = "SELECT user_id, id_delima, nama_ahli, IC_ahli, jenis_ahli, jenis_jantina, Tingkatan, Kelas, status, jumlah_hutang, Tahun_daftar, tahun_tamat, no_telefon 
        FROM ahli 
        WHERE jenis_ahli IN ('GURU', 'PELAJAR', 'STAF SOKONGAN')";

// Add search filter if a search criterion and value are provided
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'Kelas' || $searchColumn === 'status' || $searchColumn === 'jenis_ahli' || $searchColumn === 'Tingkatan' || $searchColumn === 'jenis_jantina') {
        // For Kelas and status, use exact match
        $sql .= " AND $searchColumn = '$searchValue'";
    } else {
        // For other columns, use a LIKE query for partial matches
        $sql .= " AND $searchColumn LIKE '%$searchValue%'";
    }
}

// Add pagination to the query
$sql .= " LIMIT $usersPerPage OFFSET $offset";

$result = $conn->query($sql);
$users = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Get total number of users for pagination
$totalUsersQuery = "SELECT COUNT(*) AS total FROM ahli WHERE jenis_ahli IN ('GURU', 'PELAJAR', 'STAF SOKONGAN')";
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'Kelas' || $searchColumn === 'status' || $searchColumn === 'jenis_ahli' || $searchColumn === 'Tingkatan' || $searchColumn === 'jenis_jantina') {
        $totalUsersQuery .= " AND $searchColumn = '$searchValue'";
    } else {
        $totalUsersQuery .= " AND $searchColumn LIKE '%$searchValue%'";
    }
}
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $usersPerPage);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    
    <title>SENARAI NAMA</title>
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
      <form id="searchForm" name="searchForm" method="get" action="PSS_senarai_nama.php" class="form_nama" style="margin: 20px auto; width: 90%;">
        <table width="100%" border="0" cellpadding="10" cellspacing="0">
          <tr>
            <td width="20%" align="right"><strong>Cari Mengikut:</strong></td>
            <td width="30%">
              <select name="search_column" id="search_column" onchange="updateSearchInput()" style="width: 100%;">
                <option value="">-- Pilih --</option>
                <option value="id_delima" <?= $searchColumn === 'id_delima' ? 'selected' : ''; ?>>ID</option>
                <option value="nama_ahli" <?= $searchColumn === 'nama_ahli' ? 'selected' : ''; ?>>Nama</option>
                <option value="IC_ahli" <?= $searchColumn === 'IC_ahli' ? 'selected' : ''; ?>>No IC</option>
                <option value="Kelas" <?= $searchColumn === 'Kelas' ? 'selected' : ''; ?>>Kelas</option>
                <option value="status" <?= $searchColumn === 'status' ? 'selected' : ''; ?>>Status</option>
				<option value="jenis_ahli" <?= $searchColumn === 'jenis_ahli' ? 'selected' : ''; ?>>Jenis Ahli</option>
                <option value="Tingkatan" <?= $searchColumn === 'Tingkatan' ? 'selected' : ''; ?>>Tingkatan</option>
                <option value="jenis_jantina" <?= $searchColumn === 'jenis_jantina' ? 'selected' : ''; ?>>Jantina</option>
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
              <input type="button" value="Reset" onclick="window.location.href='PSS_senarai_nama.php';">
            </td>
			  <td align="center" margin-top="10px">
                    <a href="library_PSS_Pendaftaran.php" class="daftar-bg daftar-button">Daftar Ahli</a>
                </td>
          </tr>
        </table>
      </form>
		 
		   
		<form id="form3" name="form3" method="post" action="PSS_user_delete.php">
  <table width="98%" height="140" border="1" align="center" cellpadding="1" cellspacing="1" class="form_nama">
	  <thead>
			    <tr>
				   <th colspan="15" align="center"><strong>Senarai Ahli Yang Berdaftar</strong></th>
		      </tr>
			   <tr>
				   <th width="50" align="center">No</th>
				   <th width="85" align="center">ID</th>
				   <th width="95" align="center">Nama</th>
				   <th width="86" align="center">No IC</th>
				   <th width="83" align="center">Jenis Ahli</th>
				   <th width="85" align="center">Jenis Jantina</th>
				   <th width="86" align="center">Tingkatan</th>
				   <th width="85" align="center">Kelas</th>
				   <th width="85" align="center">Status</th>
				   <th width="80" align="center">Jumlah Hutang</th>
				   <th width="85" align="center">Tahun Daftar</th>
				   <th width="85" align="center">Tahun Tamat</th>
				   <th width="80" align="center">No Phone</th>
				   <th width="75" align="center">Padam</th>
				   <th width="80" align="center">Kemas Kini</th>
			   </tr>
		   </thead>
    <tbody>
      <?php
            if (!empty($users)) {
                $no = $offset + 1; // Adjust numbering based on pagination

                foreach ($users as $row) {
                    ?>
            <tr>
                <td align="center"><?php echo $no; ?></td>
                <td align="center"><?php echo $row['id_delima']; ?></td>
                <td align="center"><?php echo $row['nama_ahli']; ?></td>
                <td align="center"><?php echo $row['IC_ahli']; ?></td>
				<td align="center"><?php echo $row['jenis_ahli']; ?></td>
                <td align="center"><?php echo $row['jenis_jantina']; ?></td>
                <td align="center"><?php echo $row['Tingkatan']; ?></td>
				<td align="center"><?php echo $row['Kelas']; ?></td>
                <td align="center"><?php echo $row['status']; ?></td>
                <td align="center">RM<?php echo $row['jumlah_hutang']; ?></td>
                <td align="center"><?php echo $row['Tahun_daftar']; ?></td>
                <td align="center"><?php echo $row['tahun_tamat']; ?></td>
				<td align="center"><?php echo $row['no_telefon']; ?></td>
                <td align="center"><input type="checkbox" name="delete_ids[]" value="<?php echo $row['user_id']; ?>"></td>
                <td align="center" margin-top="10px">
                    <a href="PSS_user_update.php?id=<?php echo $row['id_delima']; ?>" class="edit-bg edit-button">Edit</a>
                </td>
            </tr>
            <?php
            $no++;
        }
    } else {
        ?>
        <tr><td colspan="15" align="center">Tiada ahli dijumpai</td></tr>
        <?php
    }
    $conn->close();
    ?>
		
		<tr>
        <td colspan="15" align="center" height="50">
            <input type="submit" name="Delete" id="Delete" value="Padam">
        </td>
    </tr>
		
    </tbody>
  </table>
</form>
		 <br>

		 <!-- Pagination -->
      <div class="form_nama" style="text-align: center;">
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
		 
		 <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
		 
     
      <!-- JavaScript to update search input based on selected column -->
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
  } else if (searchColumn === 'status') {
    searchValueContainer.innerHTML = `
      <select name="search_value" id="search_value" style="width: 100%;">
        <option value="">-- Pilih Status --</option>
        <?php
        $status_ahliOptions = ["Aktif", "Tidak Aktif", "Berhenti", "Senarai Hitam", "Tamat Perkhidmatan", "Bertukar", "Tamat Persekolahan"];
        foreach ($status_ahliOptions as $status_ahli) {
            echo '<option value="' . $status_ahli . '">' . $status_ahli . '</option>';
        }
        ?>
      </select>
    `;
  } else if (searchColumn === 'jenis_ahli') {
    searchValueContainer.innerHTML = `
      <select name="search_value" id="search_value" style="width: 100%;">
        <option value="">-- Pilih Jenis Ahli --</option>
        <?php
        $j_ahliOptions = ["GURU", "PELAJAR", "STAF SOKONGAN"];
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
  } else if (searchColumn === 'jenis_jantina') {
    searchValueContainer.innerHTML = `
      <select name="search_value" id="search_value" style="width: 100%;">
        <option value="">-- Pilih Jantina --</option>
        <?php
        $jantinaOptions = ["Lelaki", "Perempuan"];
        foreach ($jantinaOptions as $jantina) {
            echo '<option value="' . $jantina . '">' . $jantina . '</option>';
        }
        ?>
      </select>
    `;
  } else {
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




		 
		 


