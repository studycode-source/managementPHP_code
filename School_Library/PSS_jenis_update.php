<?php
include('dblibrary.php');

// Check if 'jenis_bahan' is set in GET request
if (isset($_GET['jenis_bahan'])) {
    $jenis_bahan = $_GET['jenis_bahan'];

    // Fetch jenis_bahan details
    $sql = "SELECT * FROM senarai_jenis_buku WHERE jenis_bahan = '$jenis_bahan'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $rows = $result->fetch_assoc();
        
        $jenis_bahan = $rows['jenis_bahan'];
        
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
    <title>KEMAS KINI JENIS BAHAN</title>
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
                    <td colspan="2" align="center"><strong>Kemas kini Jenis Bahan</strong></td>
                </tr>
                <tr>
                <td width="174" align="center"><strong>Jenis Bahan</strong></td>
                <td><input type="text" id="addjenis_bahantxt" name="addjenis_bahantxt" value="<?php echo isset($jenis_bahan) ? $jenis_bahan: ''; ?>" style="width:570px;" required></td>
            </tr>
                
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="SaveChanges" value="Simpan">
                        <input type="reset" value="Reset">
                    </td>
                </tr>
                <tr><td colspan="2"  align="center"><strong><a href="PSS_jenis_buku.php">Kembali</a></strong></td></tr>
            </tbody>
        </table>
    </form>

    <?php
   if (isset($_POST['SaveChanges'])) {
    $old_jenis_bahan = $_GET['jenis_bahan']; // The original jenis_bahan from the URL
    $new_jenis_bahan = $_POST['addjenis_bahantxt']; // The new jenis_bahan entered by the user
    

    
    // Check if the new ID is unique before updating
    $check_sql = "SELECT jenis_bahan FROM senarai_jenis_buku WHERE jenis_bahan = '$new_jenis_bahan' AND jenis_bahan != '$old_jenis_bahan'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        echo "<script>alert('jenis bahan sudah digunakan. Sila masukkan Rak lain.');</script>";
    } else {
        
        $update_sql = "UPDATE senarai_jenis_buku SET 
            jenis_bahan = '$new_jenis_bahan'
            WHERE jenis_bahan = '$old_jenis_bahan'";

        if ($conn->query($update_sql) === TRUE) {
            echo '<script type="text/javascript">
                alert("Maklumat Jenis Bahan Telah Dikemas Kini");
                window.location = "PSS_jenis_buku.php"; // Redirect to PSS page
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
