<?php
include('dblibrary.php'); // Ensure your database connection is included

// Retrieve no_perolehan and id_delima from the URL
$no_perolehan = isset($_GET['no_perolehan']) ? $_GET['no_perolehan'] : '';
$id_delima = isset($_GET['id_delima']) ? $_GET['id_delima'] : '';

// Initialize variables
$jumlah_hari_transaksi = 0;
$total_denda = 0;

if (isset($_POST['submit'])) {
    // Retrieve form data
    $id_delima = $_POST['id_delima']; // Manually entered by admin/librarian
    $no_perolehan = $_POST['no_perolehan']; // Manually entered by admin/librarian
    $return_date = $_POST['date_pulang'];
    $return_time = $_POST['time_pulang'];

    // Fetch jumlah_hari_pinjaman, tarikh_pinjam, and tarikh_perlu_pulang from the buku and transaksi tables
    $sql_fetch_data = "SELECT b.jumlah_hari_pinjaman, t.tarikh_pinjam 
                       FROM buku b
                       INNER JOIN transaksi t ON b.book_id = t.book_id
                       WHERE b.no_perolehan = '$no_perolehan'";
    $result = $conn->query($sql_fetch_data);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $jumlah_hari_pinjaman = $row['jumlah_hari_pinjaman'];
        $tarikh_pinjam = $row['tarikh_pinjam'];

        // Calculate the due date (tarikh_perlu_pulang) dynamically
        $tarikh_perlu_pulang = date('Y-m-d', strtotime($tarikh_pinjam . " + $jumlah_hari_pinjaman days"));

        // Calculate the number of days the user has been borrowing the book (jumlah_hari_transaksi)
        $return_date_obj = new DateTime($return_date);
        $borrow_date_obj = new DateTime($tarikh_pinjam);
        $interval = $return_date_obj->diff($borrow_date_obj);
        $jumlah_hari_transaksi = $interval->days;

        // Calculate the number of days late
        $due_date_obj = new DateTime($tarikh_perlu_pulang);
        $days_late = $return_date_obj > $due_date_obj ? $return_date_obj->diff($due_date_obj)->days : 0;

        // Determine the transaction status and total fine
        if ($days_late > 0) {
            $transaksi_status = "Lewat";
            $total_denda = $days_late * 1; // RM1 per day
        } else {
            $transaksi_status = "Dikembalikan";
            $total_denda = 0; // No fine if returned on or before the due date
        }

        // Check if id_delima exists in the ahli table
        $sql_check_id_delima = "SELECT user_id FROM ahli WHERE id_delima = '$id_delima'";
        $result_check_id_delima = $conn->query($sql_check_id_delima);

        if ($result_check_id_delima->num_rows > 0) {
            // id_delima exists, proceed with the transaction
            $row_check_id_delima = $result_check_id_delima->fetch_assoc();
            $user_id = $row_check_id_delima['user_id'];

            // Update the transaksi table
            $sql_update = "UPDATE transaksi 
                           SET tarikh_pulang = '$return_date', 
                               masa_pemulangan = '$return_time', 
                               status_transaksi = '$transaksi_status', 
                               jumlah_denda = '$total_denda',
                               jumlah_hari_transaksi = '$jumlah_hari_transaksi'
                           WHERE book_id = (SELECT book_id FROM buku WHERE no_perolehan = '$no_perolehan')
                           AND user_id = '$user_id'";
            
            if ($conn->query($sql_update)) {
                // Update the book status to "Ada"
                $sql_update_status = "UPDATE buku SET status_buku = 'Ada' WHERE no_perolehan = '$no_perolehan'";
                if ($conn->query($sql_update_status)) {
					
                     $sql_update_hutang = "UPDATE ahli 
                                          SET jumlah_hutang = jumlah_hutang + $total_denda 
                                          WHERE id_delima = '$id_delima'";

                    if ($conn->query($sql_update_hutang)) {
                        // Redirect to library_senarai_pulang.php after successful return
                        echo '<script>alert("Buku Berjaya Dikembalikan"); window.location.href = "library_senarai_pulang.php";</script>';
                    } else {
                    echo '<script>alert("Error updating book status: ' . $conn->error . '")</script>';
                }
            } else {
                echo '<script>alert("Error: ' . $conn->error . '")</script>';
            }
        } else {
            // id_delima does not exist
            echo '<script>alert("ID Delima tidak dijumpai dalam sistem.")</script>';
        }
    } else {
        echo '<script>alert("No perolehan tidak dijumpai")</script>';
    }
}
}

// Check if no_perolehan is passed via GET request
if (isset($_GET['no_perolehan'])) {
    // Fetch buku and transaksi data
    $no_perolehan = $_GET['no_perolehan'];
    $sql = "SELECT b.*, t.jumlah_hari_transaksi, t.tarikh_pinjam 
            FROM buku b
            INNER JOIN transaksi t ON b.book_id = t.book_id
            WHERE b.no_perolehan = '$no_perolehan'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $rows = $result->fetch_assoc();

        // Calculate jumlah_hari_transaksi and total_denda for the form
        $current_date = date('Y-m-d'); // Use the current date for calculation
        $return_date_obj = new DateTime($current_date);
        $borrow_date_obj = new DateTime($rows['tarikh_pinjam']);
        $interval = $return_date_obj->diff($borrow_date_obj);
        $jumlah_hari_transaksi = $interval->days;

        // Calculate the due date (tarikh_perlu_pulang) dynamically
        $tarikh_perlu_pulang = date('Y-m-d', strtotime($rows['tarikh_pinjam'] . " + " . $rows['jumlah_hari_pinjaman'] . " days"));

        // Calculate the number of days late
        $due_date_obj = new DateTime($tarikh_perlu_pulang);
        $days_late = $return_date_obj > $due_date_obj ? $return_date_obj->diff($due_date_obj)->days : 0;

        // Calculate the total fine
        $total_denda = $days_late * 1; // RM1 per day
    } else {
        echo "No perolehan tidak dijumpai";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>PEMULANGAN BAHAN</title>
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
        // Fetch buku and transaksi data
        $no_perolehan = $_GET['no_perolehan'];
        $sql = "SELECT b.*, t.jumlah_hari_transaksi, t.tarikh_perlu_pulang 
                FROM buku b
                INNER JOIN transaksi t ON b.book_id = t.book_id
                WHERE b.no_perolehan = '$no_perolehan'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $rows = $result->fetch_assoc();
    ?>
	  
	  <!-- Return Form -->
      <form id="form5" name="form5" method="post" action="library_return.php">
        <table class="form_pulang" width="509" height="221" border="1" cellpadding="1" cellspacing="1" align="center">
          <tbody> 
            <tr>
              <td colspan="5" align="center"><strong>Pemulangan Bahan</strong></td>
            </tr>
            <tr>
              <td width="174" align="center"><strong>Nombor Perolehan</strong></td>
              <td width="313">
                 <input type="text" name="no_perolehan" value="<?php echo isset($no_perolehan) ? $no_perolehan : ''; ?>" style="width:350px;" required>
              </td>
            </tr>
            <tr>
                <td><strong>Jumlah Hari Pinjam</strong></td>
                <td>
                    <input type="text" name="jumlah_hari_pinjaman" value="<?php echo $rows['jumlah_hari_pinjaman']; ?>" style="text-align: center;" readonly>
                </td>
            </tr>
            <<tr>
				<td><strong>Bilangan Hari Bahan Dipinjam</strong></td>
				<td>
					<input type="text" name="jumlah_hari_transaksi" value="<?php echo $jumlah_hari_transaksi; ?>" style="text-align: center;" readonly>
				</td>
			</tr>

            <tr>
                <td><strong>Tarikh Perlu Pulang</strong></td>
                <td>
                    <input type="date" name="tarikh_perlu_pulang" value="<?php echo $rows['tarikh_perlu_pulang']; ?>" style="text-align: center;" readonly>
                </td>
            </tr>
            <tr>
              <td width="174" align="center"><strong>Nombor ID</strong></td>
              <td>
                <input type="text" id="txtID" name="id_delima" value="<?php echo isset($id_delima) ? $id_delima : ''; ?>" style="width:350px;" readonly>
              </td>
            </tr>
            <tr>
              <td><strong>Tarikh Pulang</strong></td>
              <td><input type="date" name="date_pulang" id="date_pulang" required></td>
            </tr>
            <tr>
              <td><strong>Masa Pulang</strong></td>
              <td><input type="time" name="time_pulang" id="time_pulang" required></td>
            </tr>
            <tr>
				<td width="174" align="center"><strong>Jumlah Denda</strong></td>
				<td><input type="text" id="txtdenda" name="total_denda" value="<?php echo $total_denda; ?>" style="width:350px;" readonly></td>
			</tr>
            <tr>
              <td colspan="2" align="center">
                <input type="submit" name="submit" value="Pulang">
                <input type="reset" value="Reset"> 
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center" height="50">
                <strong><a href="library_senarai_peminjam.php">Kembali</a></strong>
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