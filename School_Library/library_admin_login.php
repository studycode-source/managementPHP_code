<?php
include("config.php");
   session_start();
   $error='';
   if($_SERVER["REQUEST_METHOD"] == "POST") {
   
      // username and password sent from form 
      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 

    $sql = "SELECT * FROM ahli WHERE nama_ahli = '$myusername' AND id_delima = '$mypassword' AND jenis_ahli = 'ADMIN'";
	   
    $result = mysqli_query($db,$sql);      
      $row = mysqli_num_rows($result);      
      $count = mysqli_num_rows($result);

    if($count == 1) {
	  
         // session_register("myusername");
         $_SESSION['login_user'] = $myusername;
         header("location: library_main_page.php");
      } else {
         $error = "Your Login Name or Password is invalid";
      }
	   
	   // After successful login
	   if($count == 1) {
    $admin = mysqli_fetch_assoc($result); // Fetch the admin details
    $_SESSION['login_user'] = $admin['nama_ahli']; // Store username in session
    $_SESSION['admin_id'] = $admin['user_id']; // Store user ID in session for future use
    header("location: library_main_page.php");
    exit();
}
   }
?>




<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>SISTEM PENGURUSAN PUSAT SUMBER SMK DATO'ONN - Admin Login-</title>
  <link rel="stylesheet" type="text/css" href="config/css/PSS.css">
  <link href="css/bootstrap-4.4.1.css" rel="stylesheet">
</head>
<body>
  <div class="bg"></div>
  <main class="menu_content">
    <header class="PSS_header">
      <h1><strong>SISTEM PENGURUSAN PUSAT SUMBER SMK DATO'ONN</strong></h1>
    </header>
    <div class="login-container">
      <div class="login-header">Admin Login</div>
      <div class="login-body">
        <form action="" method="POST">
          <label for="username">Nama Pengguna:</label>
          <input type="text" name="username" placeholder="Masukkan Nama Admin" class="box" required>
          <label for="password">ID Admin:</label>
          <input type="password" name="password" placeholder="Masukkan kata laluan" class="box" required>
          <input type="submit" name="submit" id="submit" value="Login">
          <p><a href="index.php">Back</a></p>
        </form>
        <div class="error-message"><?php echo isset($error) ? $error : ''; ?></div>
      </div>
    </div>
  </main>
</body>
</html>
