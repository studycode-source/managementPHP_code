<?php
include('session.php');

// Ensure the user is logged in and fetch admin details
if (!isset($_SESSION['admin_id'])) {
    header("Location: library_admin_login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'school_library_system');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get admin ID from the session (to show logged-in admin's profile)
$admin_id = $_SESSION['admin_id'];

// Initialize variables
$proname = '';
$projantina = '';
$promobile = '';
$proimguser = '';
$id = '';


// Fetch admin details for logged-in user
$sql = "SELECT * FROM ahli WHERE user_id = '$admin_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $proname = $admin['nama_ahli']; 
    $projantina = $admin['jenis_jantina'];
    $promobile = $admin['no_telefon'];
    $proimguser = $admin['gambar_ahli'];
	
    
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Register'])) {
    $id = $_POST['txtID'];
    $name = $_POST['txtName'];
    $ic = $_POST['txtIC'];
    $jantina = $_POST['jantina2'];
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

    // Prepare SQL Insert query. Adjust column names as needed.
    $sql = "INSERT INTO ahli (id_delima, nama_ahli, IC_ahli, jenis_ahli, jenis_jantina, no_telefon, gambar_ahli) 
            VALUES ('$id', '$name', '$ic', 'ADMIN', '$jantina', '$mobile', '$pic')";
			
			if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Admin Berjaya Didaftar")</script>';
    } else {
        echo '<script>alert("Error: ")</script>' . $sql . "<br>" . $conn->error;
    }
}
    
    
    
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
  <head>
	  
    
    <title>LAMAN UTAMA</title>
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
		 
	<!-- display admin information after login -->
	<form id="form1" name="form1" method="post">
      <table class="form_section"  width="900" height="289" border="3" align="center">
          <tbody>
              <tr>
                  <th width="167" height="279" scope="col">
                      <img src="images/<?php echo $proimguser; ?>" alt="Admin Image" width="200" height="200">
                  </th>
                  <th width="416" scope="col">
                      <p>PROFIL</p>
                      <label for="name"><br>Nama:</label>
                      <input type="text" name="txtName" id="txtName" value="<?php echo $proname; ?>" readonly style="width:400px;"><br>
                      <label for="jantina"><br>Jantina:</label>
                      <input width="600" height="279"  type="jantina" name="jantina2" id="jantina2" value="<?php echo $projantina; ?>" readonly style="width:400px;"><br>
                      <label for="mobile"><br>No-Tel:</label>
                      <input width="600" height="279" type="text" name="txtMobile" id="txtMobile" value="<?php echo $promobile; ?>" readonly style="width:400px;"><br>
                  </th>
              </tr>
          </tbody>
      </table>
  </form>
	
	<!-- Registration Form -->
  <p><form id="form2" name="form2" method="post" enctype="multipart/form-data">
    <table class="form_section" width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
        <tbody>
			 <tr>
				   <td colspan="2" align="center"><strong>Mendaftar Admin</strong></td>
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
                <td><input type="text" id="txtIC" name="txtIC" value="<?php echo isset($ic) ? $ic : ''; ?>" style="width:570px;" ></td>
            </tr>
            <tr>
                <td height="41" align="left"><strong>Jantina</strong></td>
                <td>
                    <select name="jantina2" id="jantina2" style="width:570px;" required>
                        <option value="" align="center">-- Pilih jantina --</option> 
                        <?php
                        include('dblibrary.php');
                        $enum_values = ["Lelaki", "Perempuan"];
                        foreach ($enum_values as $value) {
                            echo '<option value="' . $value . '">' . $value . '</option>';
                        }
                        $conn->close();
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="left"><strong>Nombor Telefon</strong></td>
                <td><input type="text" id="txtMobile" name="txtMobile" style="width:570px;" ></td>
            </tr>
            <tr>
                <td height="54"><strong>Gambar Admin</strong></td>
                <td><input type="file" name="photo" id="photo"></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="Register" value="Daftar">
                    <input type="reset" value="Reset">
                </td>
            </tr>
        </tbody>
    </table>
</form></p>
	
	 <!-- Admin List with Edit and Delete options -->
   <form id="form3" name="form3" method="post" action="admin_delete_library.php">
       <table class="form_section" width="90%" height="155" border="1" cellpadding="1" cellspacing="1" align="center">
		   <thead>
			    <tr>
				   <th colspan="15" align="center"><strong>Senarai Admin</strong></th>
		      </tr>
			   <tr>
				   <th width="84" height="72" align="center">No</th>
				   <th width="81" align="center">No ID</th>
				   <th width="81" align="center">Nama</th>
				   <th width="81" align="center">No IC</th>
				   <th width="81" align="center">Jantina</th>
				   <th width="81" align="center">Number Phone</th>
				   <th width="81" align="center">Padam</th>
				   <th width="87" align="center">Kemas Kini</th>
			   </tr>
		   </thead>
           <tbody>
    <?php
    $conn = new mysqli('localhost', 'root', '', 'school_library_system');
    $sql = "SELECT user_id, id_delima, nama_ahli, IC_ahli, jenis_jantina, no_telefon FROM ahli WHERE jenis_ahli = 'ADMIN'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $admins = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
        $no = 1;

        foreach ($admins as $row) {
            ?>
            <tr>
                <td align="center"><?php echo $no; ?></td>
                <td align="center"><?php echo $row['id_delima']; ?></td>
                <td align="center"><?php echo $row['nama_ahli']; ?></td>
                <td align="center"><?php echo $row['IC_ahli']; ?></td>
                <td align="center"><?php echo $row['jenis_jantina']; ?></td>
                <td align="center"><?php echo $row['no_telefon']; ?></td>
                <td align="center"><input type="checkbox" name="delete_ids[]" value="<?php echo $row['user_id']; ?>"></td>
                <td align="center" margin-top="10px">
                    <a href="library_admin_update.php?id=<?php echo $row['id_delima']; ?>" class="edit-bg edit-button">Edit</a>
                </td>
            </tr>
            <?php
            $no++;
        }
    } else {
        ?>
        <tr><td colspan="15" align="center">Tiada admin dijumpai</td></tr>
        <?php
    }
    $conn->close();
    ?>
			   <tr>
        <td colspan="15" align="center">
            <input type="submit" name="Delete" id="Delete" value="Padam">
        </td>
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


