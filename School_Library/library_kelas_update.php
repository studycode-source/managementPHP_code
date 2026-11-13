<?php
include('dblibrary.php');

// Check if 'value' is set in GET request
if (isset($_GET['value'])) {
    $value = $_GET['value'];

    // Fetch kelas details
    $sql = "SELECT * FROM senarai_kelas WHERE value = '$value'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $rows = $result->fetch_assoc();
        
        $value = $rows['value'];
        
    } else {
        echo "Tiada kelas dijumpai dalam rekod";
        exit();
    }
} else {
    echo "Ralat: Tiada kelas diberikan";
    exit();
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEMAS KINI KELAS</title>
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
	  


    <form id="form9" name="form9" method="post" enctype="multipart/form-data">
        <table class="form_nama" width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><strong>Kemas kini Kelas</strong></td>
                </tr>
                <tr>
                <td width="174" align="center"><strong>Nama kelas</strong></td>
                <td><input type="text" id="addkelastxt" name="addkelastxt" value="<?php echo isset($value) ? $value : ''; ?>" style="width:570px;" required></td>
            </tr>
                
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="SaveChanges" value="Simpan">
                        <input type="reset" value="Reset">
                    </td>
                </tr>
                <tr><td colspan="2"  align="center"><strong><a href="add_class.php">Kembali</a></strong></td></tr>
            </tbody>
        </table>
    </form>
		</p>

    <?php
   if (isset($_POST['SaveChanges'])) {
    $old_value = $_GET['value']; // The original value from the URL
    $new_value = $_POST['addkelastxt']; // The new value entered by the user
    

    
    // Check if the new ID is unique before updating
    $check_sql = "SELECT value FROM senarai_kelas WHERE value = '$new_value' AND value != '$old_value'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        echo "<script>alert('Nama Kelas sudah digunakan. Sila masukkan Kelas lain.');</script>";
    } else {
        // Update using user_id to prevent issues with changing primary keys
        $update_sql = "UPDATE senarai_kelas SET 
            value = '$new_value'
            WHERE value = '$old_value'";

        if ($conn->query($update_sql) === TRUE) {
            echo '<script type="text/javascript">
                alert("Maklumat Kelas Telah Dikemas Kini");
                window.location = "add_class.php"; // Redirect to main page
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
