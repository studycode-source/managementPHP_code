<?php
include('dblibrary.php');

// Check if 'id' is set in GET request
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch admin details
    $sql = "SELECT * FROM ahli WHERE id_delima = '$id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $rows = $result->fetch_assoc();
        
        $name = $rows['nama_ahli'];
        $ic = $rows['IC_ahli'];
        $jantina = $rows['jenis_jantina'];
        $mobile = $rows['no_telefon'];
        $imguser = $rows['gambar_ahli']; // Existing image
    } else {
        echo "Tiada admin dijumpai dengan ID ini";
        exit();
    }
} else {
    echo "Ralat: Tiada ID diberikan";
    exit();
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEMAS KINI ADMIN</title>
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


    <form id="form9" name="form9" method="post" enctype="multipart/form-data">
        <table class="form_section" width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><strong>Kemas kini Admin</strong></td>
                </tr>
                <tr>
                    <td width="174" align="left"><strong>Nombor ID </strong></td>
                    <td><input type="text" id="txtID" name="txtID" value="<?php echo $id; ?>" style="width:570px;" required ></td>
                </tr>
                <tr>
                    <td align="left"><strong>Nama</strong></td>
                    <td><input type="text" id="txtName" name="txtName" value="<?php echo $name; ?>" style="width:570px;" required></td>
                </tr>
                <tr>
                    <td width="174" align="left"><strong>Nombor IC</strong></td>
                    <td><input type="text" id="txtIC" name="txtIC" value="<?php echo $ic; ?>" style="width:570px;" required></td>
                </tr>
                <tr>
                    <td height="41" align="left"><strong>Jantina</strong></td>
                    <td>
                        <select name="jantina2" id="jantina2" style="width:570px;" required>
                            <option value="" align="center">-- Pilih jantina --</option>
                            <option value="Lelaki" <?php if($jantina == "Lelaki") echo "selected"; ?>>Lelaki</option>
                            <option value="Perempuan" <?php if($jantina == "Perempuan") echo "selected"; ?>>Perempuan</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td align="left"><strong>Nombor Telefon</strong></td>
                    <td><input type="text" id="txtMobile" name="txtMobile" value="<?php echo $mobile; ?>" style="width:570px;" ></td>
                </tr>
                <tr>
                    <td height="54"><strong>Gambar Admin</strong></td>
                    <td>
                        <input type="file" name="photo" id="photo">
                        <br>
                        <img src="images/<?php echo $imguser; ?>" alt="Gambar Admin" width="100">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="SaveChanges" value="Simpan">
                        <input type="reset" value="Reset">
                    </td>
                </tr>
                <tr><td colspan="2"  align="center"><strong><a href="library_main_page.php">Kembali</a></strong></td></tr>
            </tbody>
        </table>
    </form>

    <?php
   if (isset($_POST['SaveChanges'])) {
    $old_id = $_GET['id']; // The original id_delima from the URL
    $new_id = $_POST['txtID']; // The new id_delima entered by the user
    $name = $_POST['txtName'];
    $ic = $_POST['txtIC'];
    $jantina = $_POST['jantina2'];
    $mobile = $_POST['txtMobile'];

    $pic = $imguser; // Default to existing image if not updated
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
        // Update using user_id to prevent issues with changing primary keys
        $update_sql = "UPDATE ahli SET 
            id_delima = '$new_id', 
            nama_ahli = '$name', 
            IC_ahli = '$ic', 
            jenis_jantina = '$jantina', 
            no_telefon = '$mobile',
            gambar_ahli = '$pic'
            WHERE id_delima = '$old_id'";

        if ($conn->query($update_sql) === TRUE) {
            echo '<script type="text/javascript">
                alert("Maklumat Admin Telah Dikemas Kini");
                window.location = "library_main_page.php"; // Redirect to main page
                </script>';
        } else {
            echo "Ralat mengemas kini rekod: " . $conn->error;
        }
    }
}


$conn->close();
?>
		 </main>
</body>
</html>
