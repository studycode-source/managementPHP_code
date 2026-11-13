<?php
include('dblibrary.php'); // Include the database connection file once at the beginning


$sql = "SELECT value FROM senarai_kelas";
			$result = $conn->query($sql);
			$kelasOptions = [];
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$kelasOptions[] = $row['value'];
				}
			}
// Check if 'id' is set in GET request
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM ahli WHERE id_delima = '$id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $rows = $result->fetch_assoc();
        
        $name = $rows['nama_ahli'];
        $ic = $rows['IC_ahli'];
        $tingkatan = $rows['Tingkatan'];
        $kelas = $rows['Kelas'];
        $jantina = $rows['jenis_jantina'];
        $status_ahli = $rows['status'];
        $hutang = $rows['jumlah_hutang'];
        $start_year = $rows['Tahun_daftar'];
        $end_year = $rows['tahun_tamat'];
        $mobile = $rows['no_telefon'];
        $imguser = $rows['gambar_ahli']; // Existing image
    } else {
        echo "Tiada PSS dijumpai dengan ID ini";
        exit();
    }
} else {
    echo "Ralat: Tiada ID diberikan";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>KEMAS KINI PSS</title>
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

      <form id="form10" name="form10" method="post" enctype="multipart/form-data">
        <table class="form_pss" width="60%" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
          <tbody> 
            <tr>
              <td colspan="2" align="center"><strong>Kemas Kini Pss</strong></td>
            </tr>
            <tr>
              <td width="174" align="left"><strong>Nombor ID </strong></td>
              <td><input type="text" id="txtID" name="txtID" value="<?php echo isset($id) ? $id : ''; ?>" style="width:570px;" required></td>
            </tr>
            <tr>
              <td align="left"><strong>Nama</strong></td>
              <td><input type="text" id="txtName" name="txtName" value="<?php echo isset($name) ? $name : ''; ?>" style="width:570px;" required></td>
            </tr>
            <tr>
              <td width="174" align="left"><strong>Nombor IC</strong></td>
              <td><input type="text" id="txtIC" name="txtIC" value="<?php echo isset($ic) ? $ic : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
              <td height="41" align="left"><strong>Tingkatan</strong></td>
              <td>
                <select name="tingkatantxt" id="tingkatantxt" style="width:570px;">
                  <option value="" align="center">-- Pilih tingkatan --</option> 
                  <?php
                  $enum_values = ["Kelas Khas", "1", "2", "3", "4", "5", "6"];
                  foreach ($enum_values as $value) {
                      echo '<option value="' . $value . '" ' . ($tingkatan == $value ? 'selected' : '') . '>' . $value . '</option>';
                  }
                  ?>
                </select>
              </td>
            </tr>
           <tr>
			  <td height="41" align="left"><strong>Kelas</strong></td>
			  <td>
				<select name="kelastxt" id="kelastxt" style="width:570px;">
				  <option value="" align="center">-- Pilih Kelas --</option> 
				  <?php foreach ($kelasOptions as $value): ?>
					<option value="<?= $value; ?>" <?= ($kelas == $value) ? 'selected' : ''; ?>><?= $value; ?></option>
				  <?php endforeach; ?>
				</select>
				  
				  <td align="center" margin-top="10px">
                    <a href="add_class.php" class="edit-bg edit-button">Tambah</a>
                </td>
			  </td>
			</tr>
            <tr>
              <td height="41" align="left"><strong>Jantina</strong></td>
              <td>
                <select name="jantina2" id="jantina2" style="width:570px;">
                  <option value="" align="center">-- Pilih jantina --</option> 
                  <?php
                  $enum_values = ["Lelaki", "Perempuan"];
                  foreach ($enum_values as $value) {
                      echo '<option value="' . $value . '" ' . ($jantina == $value ? 'selected' : '') . '>' . $value . '</option>';
                  }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td height="41" align="left"><strong>Status Ahli</strong></td>
              <td>
                <select name="sta_ahli2" id="sta_ahli2" style="width:570px;">
                  <option value="" align="center">-- Pilih status ahli --</option> 
                  <?php
                  $enum_values = ["Aktif", "Tidak Aktif", "Berhenti", "Senarai Hitam", "Tamat Perkhidmatan", "Bertukar", "Tamat Persekolahan"];
                  foreach ($enum_values as $value) {
                      echo '<option value="' . $value . '" ' . ($status_ahli == $value ? 'selected' : '') . '>' . $value . '</option>';
                  }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td align="left"><strong>Jumlah Hutang (RM)</strong></td>
              <td><input type="text" id="txthutang" name="txthutang" value="<?php echo isset($hutang) ? $hutang : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
              <td align="left"><strong>Tarikh Daftar</strong></td>
              <td><input type="date" id="date_school" name="date_school" value="<?php echo isset($start_year) ? $start_year : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
              <td align="left"><strong>Tarikh </strong></td>
              <td><input type="date" id="date_end" name="date_end" value="<?php echo isset($end_year) ? $end_year : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
              <td align="left"><strong>Nombor telefon</strong></td>
              <td><input type="text" id="txtMobile" name="txtMobile" value="<?php echo isset($mobile) ? $mobile : ''; ?>" style="width:570px;"></td>
            </tr>
            <tr>
              <td height="54"><strong>Gambar Ahli</strong></td>
              <td><input type="file" name="photo" id="photo"></td>
            </tr>
            <tr>
              <td colspan="2" align="center">
                <input type="submit" name="SaveChanges" value="Simpan">
                <input type="reset" value="Reset"> 
              </td>
            </tr>
			  <tr><td colspan="2"  align="center" height="50"><strong><a href="library_senarai_PSS.php">Kembali</a></strong></td></tr>
          </tbody>
        </table>
      </form>

      <?php
      if (isset($_POST['SaveChanges'])) {
		  $old_id = $_GET['id'];
          $new_id = $_POST['txtID'];
          $name = $_POST['txtName'];
          $ic = $_POST['txtIC'];
          $tingkatan = $_POST['tingkatantxt'];
          $kelas = $_POST['kelastxt'];
          $jantina = $_POST['jantina2'];
          $status_ahli = $_POST['sta_ahli2'];
          $hutang = $_POST['txthutang'];
          $start_year = $_POST['date_school'];
          $end_year = $_POST['date_end'];
          $mobile = $_POST['txtMobile'];

          // Handle image upload
          $pic = $imguser; // Default to existing image
          if (!empty($_FILES['photo']['name'])) {
              $target_dir = "images/";
              $target_file = $target_dir . basename($_FILES["photo"]["name"]);
              if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                  $pic = basename($_FILES["photo"]["name"]); // Save only the file name
              }
          }
		
		  
		  // Check if the new ID is unique before updating
			$check_sql = "SELECT id_delima FROM ahli WHERE id_delima = '$new_id' AND id_delima != '$old_id'";
			$check_result = $conn->query($check_sql);
			if ($check_result->num_rows > 0) {
				echo "<script>alert('ID Delima sudah digunakan. Sila masukkan ID lain.');</script>";
			} else {

          $update_sql = "UPDATE ahli SET 
				id_delima = '$new_id', 
				nama_ahli = '$name', 
				IC_ahli = '$ic', 
				Tingkatan = '$tingkatan', 
				Kelas = '$kelas', 
				jenis_jantina = '$jantina',
				status = '$status_ahli',
				jumlah_hutang = '$hutang', 
				Tahun_daftar = '$start_year', 
				tahun_tamat = '$end_year', 
				no_telefon = '$mobile',
				gambar_ahli = '$pic'
			WHERE id_delima = '$old_id'";
		  
          if ($conn->query($update_sql) === TRUE) {
              echo "<script>alert('Maklumat PSS berjaya dikemas kini!');</script>";
              echo "<script>window.location.href='library_senarai_PSS.php';</script>";
          } else {
              echo "<script>alert('Ralat dalam mengemas kini maklumat PSS: " . $conn->error . "');</script>";
          }
      }
	  }

      // Close the connection only once at the end
      $conn->close();
      ?>
    </main>
  </body>
</html>