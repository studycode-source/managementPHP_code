<!doctype html>
<html>
<head>

<title>Admin-Main Asset</title>
<link rel="stylesheet" type="text/css" href="../css/admin.css">

<link href="../css/bootstrap-4.4.1.css" rel="stylesheet" type="text/css">
<link href="config/css/admin.css" rel="stylesheet" type="text/css">
<!--The following script tag downloads a font from the Adobe Edge Web Fonts server for use within the web page. We recommend that you do not modify it.-->
<script>var __adobewebfontsappname__="dreamweaver"</script>
<script src="http://use.edgefonts.net/allerta:n4:default.js" type="text/javascript"></script>

</head>

<body>	
	
<main class="menu_content">
  <header class="admin_header">
    <form id="form1" name="form1" method="post">
		
      <h1 align="center"><strong>Welcome to admin page</strong></h1>
    </form>
  </header>
  <p>
   <section class="admin_section"><strong><a href="admin_main_Home.php">Home</a> | <a href="admin_main_Staff.php">Staff</a> | <a href="admin_main_Asset.php">Asset</a> | <a href="admin_main_Booking.php">Booking</a> | <a href="admin_main_Order.php">Order</a> | <a href="admin_main_History.php">History</a></strong></section>
  </p>
	
	<form action="asset_image.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
  <p align="center"><strong>ADD ASSET</strong></p>
  <table width="517" height="297" border="1" align="center">
    <tbody>
      <tr>
        <td height="31"><strong>Asset Name</strong></td>
        <td><input name="assetname" type="text" id="assetname" size="20"></td>
      </tr>
      <tr>
        <td height="31"><strong>Asset Type</strong></td>
        <td><select name="asset_type1" id="asset_type1">
		<option value="">-- Select Asset Type --</option> 
		<?php
            include('dbschool.php');
            // Using ENUM values directly in the dropdown
            $enum_values = ["Sports", "Laboratory", "Computer Hardware", "Furniture", "Others"]; // Array of ENUM values
            foreach ($enum_values as $value) {
                echo '<option value="' . $value . '">' . $value . '</option>';
            }
            $conn->close();
            ?>
        </select></td>
      </tr>
      <tr>
        <td height="29"><strong>Asset Quantity</strong></td>
        <td><input name="assetqty" type="text" id="assetqty" size="20"></td>
      </tr>
      <tr>
        <td height="106"><strong>Asset Image</strong></td>
        <td><input type="file" name="photo" id="photo"></td>
      </tr>
		<tr>
        <td height="29"><strong>Asset Info</strong></td>
        <td><input name="assetinfo" type="text" id="assetinfo" size="20"></td>
      </tr>
      <tr>
    </tbody>
  </table>
  <p align="center">
    <input type="submit" name="submit" id="submit" value="Submit">	
	<input type="reset" name="reset" id="reset" value="Reset">
   </p>
  <p>&nbsp;</p>
</form>
  
  <?php
  // Include database connection
  include('dbschool.php');

  // Handle status update if the form is submitted
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assetstatus'])) {
      foreach ($_POST['assetstatus'] as $asset_id => $status) {
          // Update status in database
          $update_sql = "UPDATE asset SET asset_status = '$status' WHERE asset_id = $asset_id";
          $conn->query($update_sql);
      }
  }
  ?>
	
	
  <form id="form2" name="form2" method="post">
    <p align="center"><strong> ASSET LIST</strong></p>
    <p align="center">
        <strong>Asset Name:</strong>
        <input type="text" name="assetsearch" id="assetsearch"> 
        <input type="submit" name="search" id="search" value="Search">
		<input type="submit" name="submit" id="submit" value="Submit">	
    </p>
    <table width="818" height="225" border="1" align="center">
        <tbody>
            <tr>
                <td width="51" height="49" align="center"><strong>NO</strong></td>
                <td width="83" align="center"><strong>Type</strong></td>
                <td width="270" align="center"><strong>Name</strong></td>
                <td width="82" align="center"><strong>Quantity</strong></td>
                <td width="95" align="center"><strong>Info</strong></td>
                <td width="197" align="center"><strong>Status</strong></td>
            </tr>
            
            <?php
            include('dbschool.php');
            $target = "images/";
            if (isset($_GET["Page"])) {
                $page = $_GET["Page"];
            } else {
                $page = 1;
            }
            $start_from = ($page - 1) * 3;

            if (isset($_POST['search'])) {
                $name = $_POST['assetsearch'];
                $sql = "SELECT * FROM asset WHERE asset_name LIKE '%$name%' LIMIT $start_from, 3";
                $result = $conn->query($sql);
            } else {
                $sql = "SELECT * FROM asset LIMIT $start_from, 3";
                $result = $conn->query($sql);
            }

            // Start looping rows in MySQL database
            $i = 0;
            if ($result->num_rows > 0) {
                while ($rows = $result->fetch_assoc()) {
                    $i++;
            ?>    	
            <tr align="center">
                <td height="168"><?php echo $i; ?></td>
                <td><?php echo $rows['asset_type']; ?></td>
                <td>
                    <div align="center">
                        <input type="image" name="imageField" id="imageField" src="images/<?php echo $rows['imgasset']; ?>" width="200" height="200">
                        <br><?php echo $rows['asset_name']; ?>
                    </div>
                </td>
                <td><?php echo $rows['asset_qty']; ?></td>
                <td><?php echo $rows['asset_info']; ?></td>
                <td>
                    <select name="assetstatus[<?php echo $rows['asset_id']; ?>]" id="assetstatus">
                        <option value="">-- Select Status Asset --</option> 
                        <?php
                        $enum_values = ["Available", "Not Available"];
                        foreach ($enum_values as $value) {
                            echo '<option value="' . $value . '"' . ($rows['asset_status'] == $value ? ' selected' : '') . '>' . $value . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
                }
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</form>

	
	
<p>
    <section class="admin_section" align="center">
    <strong>
        <a href="admin_logout.php">Logout</a>
    </strong>
</section>
  </p> 
  
</main>
</body>
</html>