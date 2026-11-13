<!doctype html>
<html>
<head>

<title>Staff-Main Asset</title>
<link rel="stylesheet" type="text/css" href="../css/staff.css">

<link href="../css/bootstrap-4.4.1.css" rel="stylesheet" type="text/css">
<link href="config/css/staff.css" rel="stylesheet" type="text/css">
<!--The following script tag downloads a font from the Adobe Edge Web Fonts server for use within the web page. We recommend that you do not modify it.-->
<script>var __adobewebfontsappname__="dreamweaver"</script>
<script src="http://use.edgefonts.net/allerta:n4:default.js" type="text/javascript"></script>

</head>

<body>
<main class="menu_content">
  <header class="staff_header">
    <form id="form1" name="form1" method="post">
		
      <h1 align="center"><strong>Welcome to staff page</strong></h1>
    </form>
  </header>
  <p>
   <section class="staff_section"><strong><a href="staff_main_Home.php">Home</a> | <a href="staff_main_Asset.php">Asset</a> | <a href="staff_main_Booking.php">Booking</a> | <a href="staff_main_Order.php">Order</a> | <a href="staff_main_History.php">History</a></strong></section>
  </p>
	
	<form id="form2" name="form2" method="post">
    <p align="center"><strong> ASSET LIST</strong></p>
    <p align="center">
        <strong>Asset Name:</strong>
        <input type="text" name="assetsearch" id="assetsearch"> 
        <input type="submit" name="search" id="search" value="Search">
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
				<td width="197" align="center"><strong>Book/Order</strong></td>
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
                                    <div><?php echo $rows['asset_status']; ?></div>
                                </td>
                                <td>
                                    <div align="center">
										<?php if($rows['asset_status']=="Available"){ ?>
											<a href="staff_main_Booking.php?asset_name=<?php echo $rows['asset_name']; ?>">Book</a>
										<?php }else{
											
										} ?>
										
									</div>
                                    <div align="center"><a href="staff_main_Order.php?asset_name=<?php echo $rows['asset_name']; ?>">Order</a></div>
                                </td>
				 				<td width="51" height="49" > <div align="center"><a href="staff_newOrder.php">new Order</a></div></td>
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
    <section class="staff_section" align="center">
	<strong><a href="staff_logout.php">Logout</a></strong></section>
  </p>
  
  
</main>
</body>
</html>