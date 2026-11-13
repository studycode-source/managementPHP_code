<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'school_library_system');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT value FROM senarai_kelas";
$result = $conn->query($sql);
$kelasOptions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $kelasOptions[] = $row['value'];
    }
}

$id = $name = $ic = $j_ahli = $tingkatan = $kelas = $jantina = $status_ahli = $hutang = $start_year = $end_year = $mobile = $pic = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Register'])) {
    // Retrieve form data
    $id = $_POST['txtID'];
    $name = $_POST['txtName'];
    $ic = $_POST['txtIC'];
    $j_ahli = $_POST['jenis_ahli1']; // Define $j_ahli
    $tingkatan = $_POST['tingkatantxt'];
    $kelas = $_POST['kelastxt'];
    $jantina = $_POST['jantina2'];
    $status_ahli = $_POST['sta_ahli2'];
    $hutang = $_POST['txthutang'];
    $start_year = $_POST['date_school'];
    $end_year = $_POST['date_end'];
    $mobile = $_POST['txtMobile'];

    // File upload logic
    $pic = '';
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "images/"; // Ensure this folder exists
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $pic = basename($_FILES["photo"]["name"]); // Save only the file name to the database
        }
    }
	
	// Check if id_delima already exists
    $sql_check = "SELECT id_delima FROM ahli WHERE id_delima = '$id'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        echo '<script>alert("Error: ID Delima sudah wujud dalam sistem.")</script>';
    } else {


    // Prepare SQL Insert query
    $sql = "INSERT INTO ahli (id_delima, nama_ahli, IC_ahli, jenis_ahli, Tingkatan, Kelas, jenis_jantina, status, jumlah_hutang, Tahun_daftar, tahun_tamat, no_telefon, gambar_ahli) 
            VALUES ('$id', '$name', '$ic', '$j_ahli', '$tingkatan', '$kelas', '$jantina', '$status_ahli', '$hutang', '$start_year', '$end_year', '$mobile', '$pic')";

    // Execute the query
    if ($conn->query($sql)) {
        echo '<script>alert("Maklumat Ahli Berjaya Didaftar")</script>';
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
    
    <title>PENDAFTARAN</title>
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
		 
		 
		<form id="form3" name="form3" method="post" enctype="multipart/form-data">
    <table class="form_nama" width="60%" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
        <tbody> 
              <tr>
              <td colspan="2" align="center"><strong>Mendaftar Ahli</strong></td>
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
              <td height="41" align="left"><strong>Jenis Ahli</strong></td>
              <td>
                <select name="jenis_ahli1" id="jenis_ahli1" style="width:570px;">
                  <option value="" align="center">-- Pilih jenis ahli --</option> 
                  <?php
                  $enum_values = ["GURU", "PELAJAR", "STAF SOKONGAN"];
                  foreach ($enum_values as $value) {
                      echo '<option value="' . $value . '" ' . ($j_ahli == $value ? 'selected' : '') . '>' . $value . '</option>';
                  }
                  ?>
                </select>
              </td>
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
                    <input type="submit" name="Register" value="Register">
                    <input type="reset" value="Reset"> 
                </td>
				<tr><td colspan="2"  align="center" height="50"><strong><a href="library_senarai_nama.php">Kembali</a></strong></td></tr>
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
       <strong><a href="admin_library_logout.php">Logout</a></strong>
   </section></p>
</main>
</body>
		
</html>
