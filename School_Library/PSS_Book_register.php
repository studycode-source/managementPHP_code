<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'school_library_system');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing rak options
$sql = "SELECT nama_rak FROM senarai_rak";
$result = $conn->query($sql);
$rakOptions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rakOptions[] = $row['nama_rak'];
    }
}

// Fetch existing jenis_buku options
$sql = "SELECT jenis_bahan FROM senarai_jenis_buku";
$result = $conn->query($sql);
$jenis_bukuOptions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jenis_bukuOptions[] = $row['jenis_bahan'];
    }
}

// Initialize variables to avoid undefined variable warnings
$bookid = $tajuk1 = $kod = $tulis = $book_type = $price = $location = $bahasa = $bookstatus = $dayforbook = $book_year = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Register'])) {
    // Retrieve form data
    $bookid = $_POST['bookID'];
    $tajuk1 = $_POST['txttajuk'];
    $kod = $_POST['txtkod'];
    $tulis = $_POST['txttulis'];
    $book_type = $_POST['jenis_buku1'];  
    $price = $_POST['txtprice'];
    $location = $_POST['lokasitxt'];
    $bahasa = $_POST['bahasatxt'];
    $bookstatus = $_POST['stabuku2'];
    $dayforbook = $_POST['txthari'];
    $book_year = $_POST['date_book'];
    
    // Check if no_perolehan already exists
    $sql_check = "SELECT no_perolehan FROM buku WHERE no_perolehan = '$bookid'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        echo '<script>alert("Error: Nombor Perolehan sudah wujud dalam sistem.")</script>';
    } else {
        // Prepare SQL Insert query
        $sql = "INSERT INTO buku (no_perolehan, tajuk, kod_buku, penulis, Jenis_buku, harga, lokasi_rak, Jenis_bahasa, status_buku, jumlah_hari_pinjaman, daftar_buku) 
                VALUES ('$bookid', '$tajuk1', '$kod', '$tulis', '$book_type', '$price', '$location', '$bahasa', '$bookstatus', '$dayforbook', '$book_year')";

        // Execute the query
        if ($conn->query($sql)) {
            echo '<script>alert("Maklumat Buku Berjaya Didaftar")</script>';
        } else {
            echo '<script>alert("Error: ' . $conn->error . '")</script>';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    
    <title>BAHAN</title>
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
		 
		 
		 <form id="form4" name="form4" method="post" enctype="multipart/form-data">
    <table class="form_bahan" width="60%" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
        <tbody> 
            <tr>
                <td colspan="2" align="center"><strong>Mendaftar Maklumat Bahan</strong></td>
            </tr>
            <tr>
                <td width="174" align="left"><strong>Nombor Perolehan</strong></td>
                <td><input type="text" id="bookID" name="bookID" value="<?php echo isset($bookid) ? $bookid : ''; ?>" style="width:570px;" required></td>
            </tr>
			 <tr>
                <td align="left"><strong>Bahan/Tajuk Buku</strong></td>
                <td><input type="text" id="txttajuk" name="txttajuk" value="<?php echo isset($tajuk1) ? $tajuk1 : ''; ?>" style="width:570px;" required></td>
            </tr>
			 <tr>
                <td width="174" align="left"><strong>Kod Bahan</strong></td>
                <td><input type="text" id="txtkod" name="txtkod" value="<?php echo isset($kod) ? $kod : ''; ?>" style="width:570px;" required></td>
            </tr>
			<tr>
                <td width="174" align="left"><strong>Penulis</strong></td>
                <td><input type="text" id="txttulis" name="txttulis" value="<?php echo isset($tulis) ? $tulis : ''; ?>" style="width:570px;" ></td>
            </tr>
            <tr>
                <td height="41" align="left"><strong>Jenis Bahan</strong></td>
                <td>
					<select name="jenis_buku1" id="jenis_buku1" style="width:570px;">
                  <option value="" align="center">-- Pilih jenis bahan --</option> 
                  <?php foreach ($jenis_bukuOptions as $jenis_bahan): ?>
                    <option value="<?= $jenis_bahan; ?>" <?= ($book_type == $jenis_bahan) ? 'selected' : ''; ?>><?= $jenis_bahan; ?></option>
                  <?php endforeach; ?>
                </select>
				  
				  <td align="center" margin-top="10px">
                    <a href="PSS_jenis_buku.php" class="edit-bg edit-button">Tambah</a>
                </td>
                </td>
            </tr>
			<tr>
                <td width="174" align="left"><strong>Harga Bahan</strong></td>
                <td><input type="text" id="txtprice" name="txtprice" value="<?php echo isset($price) ? $price : ''; ?>" style="width:570px;" ></td>
            </tr>
            <tr>
                <td height="41" align="left"><strong>Lokasi Rak</strong></td>
                <td>
				<select name="lokasitxt" id="lokasitxt" style="width:570px;">
                  <option value="" align="center">-- Pilih Rak --</option> 
                  <?php foreach ($rakOptions as $nama_rak): ?>
                    <option value="<?= $nama_rak; ?>" <?= ($location == $nama_rak) ? 'selected' : ''; ?>><?= $nama_rak; ?></option>
                  <?php endforeach; ?>
                </select>
				  
				  <td align="center" margin-top="10px">
                    <a href="PPS_add_rak.php" class="edit-bg edit-button">Tambah</a>
                </td>
			  </td>
            </tr>
            <tr>
                <td height="41" align="left"><strong>Jenis Bahasa</strong></td>
                <td>
                    <select name="bahasatxt" id="bahasatxt" style="width:570px;">
                        <option value="" align="center">------ Pilih jenis bahasa -----</option> 
                        <?php
                        
                        $enum_values = ["Bahasa Melayu", "English", "Mandarin", "Tamil", "Bahasa Arab"];
                        foreach ($enum_values as $value) {
                      echo '<option value="' . $value . '" ' . ($bahasa == $value ? 'selected' : '') . '>' . $value . '</option>';
                  }
                        ?>
                    </select>
                </td>
            </tr>
			<tr>
                <td height="41" align="left"><strong>Status Bahan</strong></td>
                <td>
                    <select name="stabuku2" id="stabuku2" style="width:570px;" required>
                        <option value="" align="center">-- Pilih status buku --</option> 
						<?php
                  $enum_values = ["Ada", "Tiada", "Rosak", "Hilang"];
                  foreach ($enum_values as $value) {
                      echo '<option value="' . $value . '" ' . ($bookstatus == $value ? 'selected' : '') . '>' . $value . '</option>';
                  }
                  ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td height="41" align="left"><strong>Jumlah Hari Boleh Pinjam</strong></td>
                <td>
                    <select name="txthari" id="txthari" style="width:570px;" required>
                        <option value="" align="center">-- Pilih jumlah hari --</option> 
                        <?php
                        $enum_values = ["2", "3", "Tidak Boleh Dipinjam"];
                        foreach ($enum_values as $value) {
                      echo '<option value="' . $value . '" ' . ($dayforbook == $value ? 'selected' : '') . '>' . $value . '</option>';
                  }
                        ?>
                    </select>
                </td>
            </tr>
           <tr>
              <td align="left"><strong>Tarikh Daftar Bahan</strong></td>
              <td><input type="date" id="date_book" name="date_book" value="<?php echo isset($book_year) ? $book_year : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="Register" value="Register">
                    <input type="reset" value="Reset"> 
                </td>
				<tr><td colspan="2"  align="center" height="50"><strong><a href="PSS_Book.php">Kembali</a></strong></td></tr>
            </tr>
        </tbody>
    </table>
</form>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
	  
  <p><section class="PSS_section" align="center">
       <strong><a href="pss_library_logout.php">Logout</a></strong>
   </section></p>
</main>
</body>
		
</html>
