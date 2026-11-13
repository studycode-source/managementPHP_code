<?php
include('session_pss.php');

// Ensure the user is logged in and fetch pss details
if (!isset($_SESSION['pss_id'])) {
    header("Location: library_pss_login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'school_library_system');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get pss ID from the session (to show logged-in pss's profile)
$pss_id = $_SESSION['pss_id'];

// Initialize variables
$proname = '';
$protingkatan = '';
$prokelas = '';
$projantina = '';
$proimguser = '';
$id = '';


// Fetch pss details for logged-in user
$sql = "SELECT * FROM ahli WHERE user_id = '$pss_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $pss = $result->fetch_assoc();
    $proname = $pss['nama_ahli']; 
	$protingkatan = $pss['Tingkatan']; 
	$prokelas = $pss['Kelas']; 
    $projantina = $pss['jenis_jantina'];
    
    $proimguser = $pss['gambar_ahli'];
	
    
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
		  <a href="library_pss_page.php">Laman Utama</a> | <a href="PSS_list.php">PSS</a> | <a href="PSS_senarai_nama.php">Nama</a> | <a href="PSS_Book.php">Bahan</a> | <a href="PSS_senarai_peminjam.php">Peminjaman</a> | <a href="PSS_senarai_pulang.php">Pemulangan</a> | <a href="PSS_History.php">Rekod</a></strong>
	  </section></p>
		 
	<!-- display pss information after login -->
	<form id="form1" name="form1" method="post">
      <table class="form_section"  width="900" height="289" border="3" align="center">
          <tbody>
              <tr>
                  <th width="167" height="279" scope="col">
                      <img src="images/<?php echo $proimguser; ?>" alt="Pss Image" width="200" height="200">
                  </th>
                  <th width="416" scope="col">
                      <p>PROFIL PSS</p>
                      <label for="name"><br>Nama:</label>
                      <input type="text" name="txtName" id="txtName" value="<?php echo $proname; ?>" readonly style="width:400px;"><br>
					  <label for="tingkatan"><br>Tingkatan:</label>
                      <input type="text" name="tingkatantxt" id="tingkatantxt" value="<?php echo $protingkatan; ?>" readonly style="width:400px;"><br>
					  <label for="kelas"><br>Kelas:</label>
                      <input type="text" name="kelastxt" id="kelastxt" value="<?php echo $prokelas; ?>" readonly style="width:400px;"><br>
                      <label for="jantina"><br>Jantina:</label>
                      <input width="600" height="279"  type="jantina" name="jantina2" id="jantina2" value="<?php echo $projantina; ?>" readonly style="width:400px;"><br>
                      
                  </th>
              </tr>
          </tbody>
      </table>
  </form>
	
	<br>

<br>
		
  
 
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
    <script src="js/jquery-3.4.1.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed --> 
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap-4.4.1.js"></script>
	 <br> 
  <p><section class="PSS_section" align="center">
       <strong><a href="pss_library_logout.php">Logout</a></strong>
   </section></p>
</main>
</body>
		
</html>
