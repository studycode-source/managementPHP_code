<?php
include('dblibrary.php'); // Ensure your database connection is included

// Define how many books per page
$booksPerPage = 15;

// Get the current page from URL, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting row for pagination
$offset = ($page - 1) * $booksPerPage;

// Initialize search variables
$searchColumn = isset($_GET['search_column']) ? $_GET['search_column'] : '';
$searchValue = isset($_GET['search_value']) ? $_GET['search_value'] : '';

// Base SQL query
$sql = "SELECT book_id, no_perolehan, tajuk, kod_buku, penulis, Jenis_buku, harga, lokasi_rak, Jenis_bahasa, status_buku, jumlah_hari_pinjaman, daftar_buku
        FROM buku";

// Add search filter if a search criterion and value are provided
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'Jenis_buku' || $searchColumn === 'lokasi_rak' || $searchColumn === 'Jenis_bahasa' || $searchColumn === 'status_buku' || $searchColumn === 'jumlah_hari_pinjaman') {
        
        $sql .= " WHERE $searchColumn = '$searchValue'";
    } else {
        // For other columns, use a LIKE query for partial matches
        $sql .= " WHERE $searchColumn LIKE '%$searchValue%'";
    }
}

// Add pagination to the query
$sql .= " LIMIT $booksPerPage OFFSET $offset";

$result = $conn->query($sql);
$books = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Get total number of Books for pagination
$totalBooksQuery = "SELECT COUNT(*) AS total FROM buku";
if (!empty($searchColumn) && !empty($searchValue)) {
    if ($searchColumn === 'Jenis_buku' || $searchColumn === 'lokasi_rak' || $searchColumn === 'Jenis_bahasa' || $searchColumn === 'status_buku' || $searchColumn === 'jumlah_hari_pinjaman') {
        $totalBooksQuery .= " WHERE $searchColumn = '$searchValue'";
    } else {
        $totalBooksQuery .= " WHERE $searchColumn LIKE '%$searchValue%'";
    }
}
$totalBooksResult = $conn->query($totalBooksQuery);
$totalBooks = $totalBooksResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $booksPerPage);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    
    <title>SENARAI BAHAN</title>
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
		 
		 <form id="searchForm" name="searchForm" method="get" action="library_Book.php" class="form_bahan" style="margin: 20px auto; width: 90%;">
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
                <option value="lokasi_rak" <?= $searchColumn === 'lokasi_rak' ? 'selected' : ''; ?>>Lokasi Rak</option>
				<option value="Jenis_bahasa" <?= $searchColumn === 'Jenis_bahasa' ? 'selected' : ''; ?>>Bahasa</option>
                <option value="status_buku" <?= $searchColumn === 'status_buku' ? 'selected' : ''; ?>>Status Bahan</option>
                <option value="jumlah_hari_pinjaman" <?= $searchColumn === 'jumlah_hari_pinjaman' ? 'selected' : ''; ?>>Hari Boleh Pinjam</option>
              </select>
            </td>
            <td width="20%" align="right"><strong>Nilai Carian:</strong></td>
            <td width="30%">
              <div id="search_value_container">
                <!-- Default input field -->
                <input type="text" name="search_value" id="search_value" value="<?= $searchValue; ?>" style="width: 100%;">
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="4" align="center">
              <input type="submit" value="Cari">
              <input type="button" value="Reset" onclick="window.location.href='library_Book.php';">
            </td>
			  <td align="center" margin-top="10px">
                    <a href="library_Book_register.php" class="daftar-bg daftar-button">Daftar Bahan</a>
                </td>
          </tr>
        </table>
      </form>

		 
		 
		 <form id="form7" name="form7" method="post" action="book_delete_library.php">
    <table class="form_bahan" width="98%" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
		<thead>
			    <tr>
				   <td  colspan="20" align="center"><strong>Senarai Maklumat Bahan</strong></td>
		      </tr>
			   <tr>
				   <th width="40" align="center">No</th>
				   <th width="89" align="center">No Perolehan</th>
				   <th width="150" align="center">Bahan/Tajuk Buku</th>
				   <th width="81" align="center">Kod Bahan</th>
				   <th width="86" align="center">Penulis</th>
				   <th width="86" align="center">Jenis Bahan</th>
				   <th width="50" align="center">Harga Bahan</th>
				   <th width="82" align="center">Lokasi Rak</th>
				   <th width="82" align="center">Jenis Bahasa</th>
				   <th width="70" align="center">Status Bahan</th>
				   <th width="60" align="center">Jumlah Hari Boleh Pinjam</th>
				   <th width="80" align="center">Tarikh Daftar Bahan</th>
				   <th width="50" align="center">Padam</th>
				   <th width="60" align="center">Kemas Kini</th>
				   <th width="85" align="center"></th>
			   </tr>
		   </thead>
        <tbody> 
        <?php
            if (!empty($books)) {
                $no = $offset + 1; // Adjust numbering based on pagination

                foreach ($books as $row) {
                    ?>
            <tr>
                <td align="center"><?php echo $no; ?></td>
                <td align="center"><?php echo $row['no_perolehan']; ?></td>
                <td align="center"><?php echo $row['tajuk']; ?></td>
                <td align="center"><?php echo $row['kod_buku']; ?></td>
				<td align="center"><?php echo $row['penulis']; ?></td>
				<td align="center"><?php echo $row['Jenis_buku']; ?></td>
                <td align="center">RM<?php echo $row['harga']; ?></td>
                <td align="center"><?php echo $row['lokasi_rak']; ?></td>
                <td align="center"><?php echo $row['Jenis_bahasa']; ?></td>
                <td align="center"><?php echo $row['status_buku']; ?></td>
                <td align="center"><?php echo $row['jumlah_hari_pinjaman']; ?></td>
				<td align="center"><?php echo $row['daftar_buku']; ?></td>
                <td align="center"><input type="checkbox" name="delete_ids[]" value="<?php echo $row['book_id']; ?>"></td>
                <td align="center" margin-top="10px">
                    <a href="library_book_update.php?bookid=<?php echo $row['no_perolehan']; ?>" class="edit-bg edit-button">Edit</a>
                </td>
				</td>
				<td align="center" margin-top="10px">
				<?php if ($row['status_buku'] == "Ada" && ($row['jumlah_hari_pinjaman'] == "2" || $row['jumlah_hari_pinjaman'] == "3")) { ?>
					<a href="library_Booking.php?no_perolehan=<?php echo $row['no_perolehan']; ?>" class="Pinjam-bg Pinjam-button">Pinjam</a>
				<?php } ?>
			</td>

            </tr>
            <?php
            $no++;
        }
    } else {
        ?>
        <tr><td colspan="20" align="center">Tiada Bahan dijumpai</td></tr>
        <?php
    }
    $conn->close();
    ?>
		
		<tr>
        <td colspan="20" align="center" height="50">
            <input type="submit" name="Delete" id="Delete" value="Padam">
        </td>
    </tr>
    </tbody>
  </table>
</form>
		 <br>

		 <!-- Pagination -->
      <div class="form_bahan" style="text-align: center;">
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

  if (searchColumn === 'Jenis_buku') {
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
  } else if (searchColumn === 'lokasi_rak') {
    // Fetch rak options from the server
    fetch('get_rak_options.php') // Create a new PHP file to fetch Rak options
      .then(response => response.json())
      .then(data => {
        // Create the dropdown for Rak
        let dropdownHTML = '<select name="search_value" id="search_value" style="width: 100%;">';
        dropdownHTML += '<option value="">-- Pilih Rak --</option>';
        data.forEach(location => {
          dropdownHTML += `<option value="${location}">${location}</option>`;
        });
        dropdownHTML += '</select>';
        searchValueContainer.innerHTML = dropdownHTML;
      })
      .catch(error => console.error('Error fetching rak options:', error));
  } else if (searchColumn === 'Jenis_bahasa') {
    searchValueContainer.innerHTML = `
      <select name="search_value" id="search_value" style="width: 100%;">
        <option value="">-- Pilih Jenis Bahasa --</option>
        <?php
        $bahasaOptions = ["Bahasa Melayu", "English", "Mandarin", "Tamil", "Bahasa Arab"];
        foreach ($bahasaOptions as $bahasa) {
            echo '<option value="' . $bahasa . '">' . $bahasa . '</option>';
        }
        ?>
      </select>
    `;
  } else if (searchColumn === 'status_buku') {
    searchValueContainer.innerHTML = `
      <select name="search_value" id="search_value" style="width: 100%;">
        <option value="">-- Pilih Status --</option>
        <?php
        $bookstatusOptions = ["Ada", "Tiada", "Rosak", "Hilang", "Dipinjam"];
        foreach ($bookstatusOptions as $bookstatus) {
            echo '<option value="' . $bookstatus . '">' . $bookstatus . '</option>';
        }
        ?>
      </select>
    `;
  } else if (searchColumn === 'jumlah_hari_pinjaman') {
    searchValueContainer.innerHTML = `
      <select name="search_value" id="search_value" style="width: 100%;">
        <option value="">-- Pilih Bilangan Hari --</option>
        <?php
        $dayforbookOptions = ["2", "3", "Tidak Boleh Dipinjam"];
        foreach ($dayforbookOptions as $dayforbook) {
            echo '<option value="' . $dayforbook . '">' . $dayforbook . '</option>';
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
       <strong><a href="admin_library_logout.php">Logout</a></strong>
   </section></p>
</main>
</body>
		
</html>

