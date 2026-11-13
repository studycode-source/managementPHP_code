<?php
include('dblibrary.php'); // Include the database connection file

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

// Check if 'bookid' is set in GET request
if (isset($_GET['bookid'])) {
    $bookid = $_GET['bookid'];

    // Fetch book details based on no_perolehan
    $sql = "SELECT * FROM buku WHERE no_perolehan = '$bookid'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $rows = $result->fetch_assoc();
        
        $tajuk1 = $rows['tajuk'];
        $kod = $rows['kod_buku'];
        $tulis = $rows['penulis'];
        $book_type = $rows['jenis_buku'];  
        $price = $rows['harga'];
        $location = $rows['lokasi_rak'];
        $bahasa = $rows['jenis_bahasa'];
        $bookstatus = $rows['status_buku'];
        $dayforbook = $rows['jumlah_hari_pinjaman'];
        $book_year = $rows['daftar_buku'];
    } else {
        echo "Tiada buku dijumpai dengan Nombor Perolehan ini";
        exit();
    }
} else {
    echo "Ralat: Tiada Nombor Perolehan diberikan";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>KEMAS KINI BAHAN</title>
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
         
      <form id="form4" name="form4" method="post" enctype="multipart/form-data">
        <table class="form_bahan" width="60%" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
          <tbody> 
            <tr>
              <td colspan="2" align="center"><strong>Mengemas kini Maklumat Bahan</strong></td>
            </tr>
            <tr>
              <td width="174" align="left"><strong>Nombor Perolehan</strong></td>
              <td><input type="text" id="bookID" name="bookID" value="<?php echo isset($bookid) ? $bookid : ''; ?>" style="width:570px;" required></td>
            </tr>
            <tr>
              <td align="left"><strong>Bahan/Tajuk Buku</strong></td>
              <td><input type="text" id="txttajuk" name="txttajuk" value="<?php echo isset($tajuk1) ? $tajuk1 : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
              <td width="174" align="left"><strong>Kod Bahan</strong></td>
              <td><input type="text" id="txtkod" name="txtkod" value="<?php echo isset($kod) ? $kod : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
              <td width="174" align="left"><strong>Penulis</strong></td>
              <td><input type="text" id="txttulis" name="txttulis" value="<?php echo isset($tulis) ? $tulis : ''; ?>" style="width:570px;"></td>
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
              </td>
            </tr>
            <tr>
              <td width="174" align="left"><strong>Harga Bahan</strong></td>
              <td><input type="text" id="txtprice" name="txtprice" value="<?php echo isset($price) ? $price : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
              <td width="174" align="left"><strong>Kuantiti Bahan</strong></td>
              <td><input type="text" id="txtqty" name="txtqty" size="20" value="<?php echo isset($bookqty) ? $bookqty : ''; ?>" style="width:570px;"></td>
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
                <select name="stabuku2" id="stabuku2" style="width:570px;">
                  <option value="" align="center">-- Pilih status bahan --</option> 
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
                <select name="txthari" id="txthari" style="width:570px;">
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
                <input type="submit" name="SaveChanges" value="Simpan">
                <input type="reset" value="Reset"> 
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center" height="50">
                <strong><a href="library_Book.php">Kembali</a></strong>
              </td>
            </tr>
          </tbody>
        </table>
      </form>

<?php
if (isset($_POST['SaveChanges'])) {
    $old_bookid = $_GET['bookid'];
    $new_bookid = $_POST['bookID'];
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

    // Check if the new ID is unique before updating
    $check_sql = "SELECT no_perolehan FROM buku WHERE no_perolehan = '$new_bookid' AND no_perolehan != '$old_bookid'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        echo "<script>alert('No Perolehan sudah digunakan. Sila masukkan No Perolehan lain.');</script>";
    } else {
        $update_sql = "UPDATE buku SET 
            no_perolehan = '$new_bookid', 
            tajuk = '$tajuk1', 
            kod_buku = '$kod',
            penulis = '$tulis',
            jenis_buku = '$book_type', 
            harga = '$price',  
            lokasi_rak = '$location',
            jenis_bahasa = '$bahasa',
            status_buku = '$bookstatus', 
            jumlah_hari_pinjaman = '$dayforbook', 
            daftar_buku = '$book_year' 
            WHERE no_perolehan = '$old_bookid'";
        
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('Maklumat buku berjaya dikemas kini!');</script>";
            echo "<script>window.location.href='library_Book.php';</script>";
        } else {
            echo "<script>alert('Ralat dalam mengemas kini maklumat buku: " . $conn->error . "');</script>";
        }
    }
}

// Close the connection only once at the end
$conn->close();
?>
    </main>
  </body>
</html>