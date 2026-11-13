<?php
include('dblibrary.php');

// Check if 'nama_rak' is set in GET request
if (isset($_GET['nama_rak'])) {
    $nama_rak = $_GET['nama_rak'];

    // Fetch nama_rak details
    $sql = "SELECT * FROM senarai_rak WHERE nama_rak = '$nama_rak'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $rows = $result->fetch_assoc();
        
        $nama_rak = $rows['nama_rak'];
        
    } else {
        echo "Tiada rak dijumpai dalam rekod";
        exit();
    }
} else {
    echo "Ralat: Tiada rak diberikan";
    exit();
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEMAS KINI RAK</title>
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


    <form id="form9" name="form9" method="post" enctype="multipart/form-data">
        <table class="form_bahan" width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><strong>Kemas kini Rak</strong></td>
                </tr>
                <tr>
                <td width="174" align="center"><strong>Nama rak</strong></td>
                <td><input type="text" id="addraktxt" name="addraktxt" value="<?php echo isset($nama_rak) ? $nama_rak: ''; ?>" style="width:570px;" required></td>
            </tr>
                
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="SaveChanges" value="Simpan">
                        <input type="reset" value="Reset">
                    </td>
                </tr>
                <tr><td colspan="2"  align="center"><strong><a href="add_rak.php">Kembali</a></strong></td></tr>
            </tbody>
        </table>
    </form>

    <?php
   if (isset($_POST['SaveChanges'])) {
    $old_nama_rak = $_GET['nama_rak']; // The original nama_rak from the URL
    $new_nama_rak = $_POST['addraktxt']; // The new nama_rak entered by the user
    

    
    // Check if the new ID is unique before updating
    $check_sql = "SELECT nama_rak FROM senarai_rak WHERE nama_rak = '$new_nama_rak' AND nama_rak != '$old_nama_rak'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        echo "<script>alert('Nama Rak sudah digunakan. Sila masukkan Rak lain.');</script>";
    } else {
        // Update using user_id to prevent issues with changing primary keys
        $update_sql = "UPDATE senarai_rak SET 
            nama_rak = '$new_nama_rak'
            WHERE nama_rak = '$old_nama_rak'";

        if ($conn->query($update_sql) === TRUE) {
            echo '<script type="text/javascript">
                alert("Maklumat Rak Telah Dikemas Kini");
                window.location = "PPS_add_rak.php"; // Redirect to PSS page
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
