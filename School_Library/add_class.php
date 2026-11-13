<?php
include('session.php');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'school_library_system');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing kelas options
$sql = "SELECT value FROM senarai_kelas";
$result = $conn->query($sql);
$kelasOptions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $kelasOptions[] = $row['value'];
    }
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Register'])) {
    $value = $_POST['addkelastxt'];
	
	$sql_check = "SELECT value FROM senarai_kelas WHERE value = '$value'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        echo '<script>alert("Error: Kelas sudah wujud dalam sistem.")</script>';
    } else {
    
    // Prepare SQL Insert query
    $sql = "INSERT INTO senarai_kelas (value) VALUES ('$value')";
    
    // Execute the query
    if ($conn->query($sql)) {
        echo '<script>alert("Maklumat kelas Berjaya ditambah")</script>';
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
	  
    
    <title>PENAMBAHAN KELAS</title>
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
	
	<!-- Registration Form -->
  <p><form id="form2" name="form2" method="post" action="add_class.php">
    <table class="form_nama" width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
        <tbody>
            <tr>
                <td colspan="2" align="center"><strong>Menambah Kelas</strong></td>
            </tr>
            <tr>
                <td width="174" align="center"><strong>Nama kelas</strong></td>
                <td><input type="text" id="addkelastxt" name="addkelastxt" value="<?php echo isset($value) ? $value : ''; ?>" style="width:570px;" required></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="Register" value="Tambah">
                    <input type="reset" value="Reset">
                </td>
            </tr>
        </tbody>
    </table>
</form></p>
	
	 <!-- kelas List with Edit and Delete options -->
   <form id="form3" name="form3" method="post" action="kelas_delete_library.php">
       <table class="form_nama" width="90%" height="155" border="1" cellpadding="1" cellspacing="1" align="center">
		   <thead>
			    <tr>
				   <th colspan="15" align="center">Senarai kelas</th>
		      </tr>
			   <tr>
				   <th width="84" height="72" align="center">No</th>
				   <th width="81" align="center">Nama Kelas</th>
				   <th width="81" align="center">Padam</th>
				   <th width="87" align="center">Kemas Kini</th>
			   </tr>
		   </thead>
           <tbody>
    <?php
    $conn = new mysqli('localhost', 'root', '', 'school_library_system');
    $sql = "SELECT kelas_id, value FROM senarai_kelas";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $class = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
        $no = 1;

        foreach ($class as $row) {
            ?>
            <tr>
                <td align="center"><?php echo $no; ?></td>
                <td align="center"><?php echo $row['value']; ?></td>
                <td align="center"><input type="checkbox" name="delete_ids[]" value="<?php echo $row['kelas_id']; ?>"></td>
                <td align="center" margin-top="10px">
                    <a href="library_kelas_update.php?value=<?php echo $row['value']; ?>" class="edit-bg edit-button">Edit</a>
                </td>
            </tr>
            <?php
            $no++;
        }
    } else {
        ?>
        <tr><td colspan="15" align="center">Tiada Kelas dijumpai</td></tr>
        <?php
    }
    $conn->close();
    ?>
			   <tr>
        <td colspan="15" align="center">
            <input type="submit" name="Delete" id="Delete" value="Padam">
        </td>
					   
    </tr>
		<tr><td colspan="15"  align="center" height="50"><strong><a href="library_Pendaftaran.php">Kembali</a></strong></td></tr>	   
</tbody>


       </table>

       
   </form>

		
  
 
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
	  
  
</main>
</body>
		
</html>
