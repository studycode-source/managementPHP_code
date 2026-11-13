<?php
include("config.php");
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Username and password sent from form 
    $myusername = mysqli_real_escape_string($db, $_POST['username']);
    $mypassword = mysqli_real_escape_string($db, $_POST['password']);

    // Query to check if the user exists and is a PSS member
    $sql = "SELECT * FROM ahli WHERE nama_ahli = '$myusername' AND id_delima = '$mypassword' AND jenis_ahli = 'PENGAWAS PSS'";
    $result = mysqli_query($db, $sql);

    if ($result) {
        $count = mysqli_num_rows($result);

        if ($count == 1) {
            // Fetch the user details
            $pss = mysqli_fetch_assoc($result);

            // Store user details in session
            $_SESSION['login_user'] = $pss['nama_ahli'];
            $_SESSION['pss_id'] = $pss['user_id']; // Store user ID in session

            // Redirect to the PSS page
            header("location: library_pss_page.php");
            exit();
        } else {
            $error = "Your Login Name or Password is invalid";
        }
    } else {
        $error = "Database query failed: " . mysqli_error($db);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>SISTEM PENGURUSAN PUSAT SUMBER SMK DATO'ONN - PSS Login-</title>
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
      <div class="login_PSS-header">Pengawas PSS Login</div>
      <div class="login_PSS-body">
        <form action="" method="POST">
          <label for="username">Nama Pengguna:</label>
          <input type="text" name="username" placeholder="Masukkan Nama Pss" class="box" required>
          <label for="password">ID PSS:</label>
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