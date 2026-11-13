<?php
include('dblibrary.php'); // Ensure your database connection is included

if (isset($_POST['submit'])) {
    // Retrieve form data
    $id_delima = $_POST['id_delima']; // Manually entered by admin/librarian
    $no_perolehan = $_POST['no_perolehan']; // Manually entered by admin/librarian
    $borrow_date = $_POST['date_pinjam'];
    $borrow_time = $_POST['time_pinjam'];

    // Fetch jumlah_hari_pinjaman from the buku table
    $sql_fetch_hari_pinjaman = "SELECT jumlah_hari_pinjaman FROM buku WHERE no_perolehan = '$no_perolehan'";
    $result = $conn->query($sql_fetch_hari_pinjaman);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $jumlah_hari_pinjaman = $row['jumlah_hari_pinjaman'];

        // Check if id_delima exists in the ahli table
        $sql_check_id_delima = "SELECT user_id FROM ahli WHERE id_delima = '$id_delima' AND status = 'Aktif'";
        $result_check_id_delima = $conn->query($sql_check_id_delima);

        if ($result_check_id_delima->num_rows > 0) {
            // id_delima exists, proceed with the transaction
            $row_check_id_delima = $result_check_id_delima->fetch_assoc();
            $user_id = $row_check_id_delima['user_id'];

            // Set jumlah_hari_transaksi to 0 initially
            $jumlah_hari_transaksi = 0;

            // Set default status
            $transaksi_status = "Sedang Meminjam"; // Default status

            // Calculate tarikh_perlu_pulang
            $tarikh_perlu_pulang = date('Y-m-d', strtotime("+$jumlah_hari_pinjaman days", strtotime($borrow_date)));

            // Insert into transaksi table using prepared statements
            $sql_insert = "INSERT INTO transaksi (tarikh_pinjam, masa_meminjam, jumlah_hari_transaksi, user_id, book_id, status_transaksi, tarikh_perlu_pulang)
                           VALUES ('$borrow_date', '$borrow_time', '$jumlah_hari_transaksi',
                           '$user_id', 
                           (SELECT book_id FROM buku WHERE no_perolehan = '$no_perolehan'),
                           '$transaksi_status', '$tarikh_perlu_pulang')";
            
            if ($conn->query($sql_insert) === TRUE) {
                // Update the book status to "Dipinjam"
                $sql_update_status = "UPDATE buku SET status_buku = 'Dipinjam' WHERE no_perolehan = '$no_perolehan'";
                if ($conn->query($sql_update_status) === TRUE) {
                    // Update jumlah_bacaan in the ahli table
                    $sql_update_jumlah_bacaan = "UPDATE ahli SET jumlah_bacaan = jumlah_bacaan + 1 WHERE user_id = '$user_id'";
                    if ($conn->query($sql_update_jumlah_bacaan) === TRUE) {
                        // Redirect to library_senarai_peminjam.php after successful booking
                        echo '<script>alert("Buku Berjaya Dipinjam"); window.location.href = "library_senarai_peminjam.php";</script>';
                    } else {
                        echo '<script>alert("Error updating jumlah_bacaan: ' . $conn->error . '")</script>';
                    }
                } else {
                    echo '<script>alert("Error updating book status: ' . $conn->error . '")</script>';
                }
            } else {
                echo '<script>alert("Error: ' . $conn->error . '")</script>';
            }
        } else {
            // id_delima does not exist
            echo '<script>alert("ID Delima tidak dijumpai dalam sistem atau status tidak aktif."); window.location.href = "library_senarai_peminjam.php";</script>';
        }
    } else {
        echo '<script>alert("No perolehan tidak dijumpai")</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>PEMINJAMAN BAHAN</title>
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
	  
	  <?php
    // Check if no_perolehan is passed via GET request
    if (isset($_GET['no_perolehan'])) {
        // Fetch buku no perolean
        $no_perolehan = $_GET['no_perolehan'];
        $sql = "SELECT * FROM buku WHERE no_perolehan = '$no_perolehan'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $rows = $result->fetch_assoc();
    ?>
	  
	  <!-- Booking Form -->
      <form id="form5" name="form5" method="post" action="library_Booking.php">
        <table class="form_pinjam" width="509" height="221" border="1" cellpadding="1" cellspacing="1" align="center">
          <tbody> 
            <tr>
              <td colspan="5" align="center"><strong>Peminjaman Bahan</strong></td>
            </tr>
            <tr>
              <td width="174" align="center"><strong>Nombor Perolehan</strong></td>
              <td width="313">
                 <input type="text" name="no_perolehan" value="<?php echo isset($no_perolehan) ? $no_perolehan : ''; ?>" style="width:350px;" required>
              </td>
            </tr>
            <tr>
              <td width="174" align="center"><strong>Nombor ID</strong></td>
              <td><input type="text" id="txtID" name="id_delima" value="<?php echo isset($id_delima) ? $id_delima : ''; ?>" style="width:350px;" required></td>
            </tr>
            <tr>
              <td><strong>Tarikh Pinjam</strong></td>
              <td><input type="date" name="date_pinjam" id="date_pinjam" required></td>
            </tr>
            <tr>
              <td><strong>Masa Pinjam</strong></td>
              <td><input type="time" name="time_pinjam" id="time_pinjam" required></td>
            </tr>
            <tr>
                <td><strong>Jumlah Hari Pinjam</strong></td>
                <td>
                    <input type="text" name="jumlah_hari_pinjaman" value="<?php echo $rows['jumlah_hari_pinjaman']; ?>" style="text-align: center;" readonly>
                </td>
            </tr>
            <tr>
              <td colspan="2" align="center">
                <input type="submit" name="submit" value="Pinjam">
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
        } else {
            echo "No perolehan tidak dijumpai";
        }
    }
    ?>
           
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