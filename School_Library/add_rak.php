<?php
include('session.php');

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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Register'])) {
    $nama_rak = $_POST['addraktxt'];
    
	$sql_check = "SELECT nama_rak FROM senarai_rak WHERE nama_rak = '$nama_rak'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        echo '<script>alert("Error: Nama Rak sudah wujud dalam sistem.")</script>';
    } else {
    // Prepare SQL Insert query
    $sql = "INSERT INTO senarai_rak (nama_rak) VALUES ('$nama_rak')";
    
    // Execute the query
    if ($conn->query($sql)) {
        echo '<script>alert("Maklumat rak Berjaya ditambah")</script>';
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
	  
    
    <title>PENAMBAHAN RAK</title>
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
  <p><form id="form2" name="form2" method="post" action="add_rak.php">
    <table class="form_bahan" width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
        <tbody>
            <tr>
                <td colspan="2" align="center"><strong>Menambah Rak</strong></td>
            </tr>
            <tr>
                <td width="174" align="center"><strong>Nama Rak</strong></td>
                <td><input type="text" id="addraktxt" name="addraktxt" value="<?php echo isset($nama_rak) ? $nama_rak : ''; ?>" style="width:570px;" required></td>
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
	
	 <!-- rak List with Edit and Delete options -->
   <form id="form3" name="form3" method="post" action="rak_delete_library.php">
       <table class="form_bahan" width="90%" height="155" border="1" cellpadding="1" cellspacing="1" align="center">
		   <thead>
			    <tr>
				   <th colspan="15" align="center">Senarai Rak</th>
		      </tr>
			   <tr>
				   <th width="84" height="72" align="center">No</th>
				   <th width="81" align="center">Nama Rak</th>
				   <th width="81" align="center">Padam</th>
				   <th width="87" align="center">Kemas Kini</th>
			   </tr>
		   </thead>
           <tbody>
    <?php
    $conn = new mysqli('localhost', 'root', '', 'school_library_system');
    $sql = "SELECT rak_id, nama_rak FROM senarai_rak";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $rak = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
        $no = 1;

        foreach ($rak as $row) {
            ?>
            <tr>
                <td align="center"><?php echo $no; ?></td>
                <td align="center"><?php echo $row['nama_rak']; ?></td>
                <td align="center"><input type="checkbox" name="delete_ids[]" value="<?php echo $row['rak_id']; ?>"></td>
                <td align="center" margin-top="10px">
                    <a href="library_rak_update.php?nama_rak=<?php echo $row['nama_rak']; ?>" class="edit-bg edit-button">Edit</a>
                </td>
            </tr>
            <?php
            $no++;
        }
    } else {
        ?>
        <tr><td colspan="15" align="center">Tiada Rak dijumpai</td></tr>
        <?php
    }
    $conn->close();
    ?>
			   <tr>
        <td colspan="15" align="center">
            <input type="submit" name="Delete" id="Delete" value="Padam">
        </td>
    </tr>
	<tr><td colspan="15"  align="center" height="50"><strong><a href="library_Book_register.php">Kembali</a></strong></td></tr>		   
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
