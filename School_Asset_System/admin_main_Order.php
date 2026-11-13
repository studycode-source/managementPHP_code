<?php
include('dbschool.php');

// Start the session
session_start();

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the Order ID from the form submission
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $order_staReason = $_POST['order_staReason'];
    
    // Update the order status and reason in the database
    $sql_update = "UPDATE `order` SET order_status = '$order_status', order_staReason = '$order_staReason' WHERE order_id = '$order_id'";
    if ($conn->query($sql_update) === TRUE) {
        echo "Order updated successfully!";
    } else {
        echo "Error updating order: " . $conn->error;
    }
}

// Fetch all order information
$sql = "SELECT * FROM `order`";
$result = $conn->query($sql);

?>

<!doctype html>
<html>
<head>

<title>Admin-Main Order</title>
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
  <form id="form2" name="form2" method="post">
	<p align="center"><strong> ORDER LIST</strong></p>
<table width="90%" height="155" border="1" cellpadding="1" cellspacing="1" align="center">    
	<tbody>
      <tr>
        <td width="84" height="72" align="center"><strong>No</strong></td>
        <td width="81" align="center"><strong>Username</strong></td>
        <td width="81" align="center"><strong>Asset</strong></td>
		<td width="81" align="center"><strong>Quantity</strong></td>
		<td width="81" align="center"><strong>Date</strong></td>
		<td width="81" align="center"><strong>Time</strong></td>
        <td width="81" align="center"><strong>Info</strong></td>
		<td width="81" align="center"><strong>Status</strong></td>
        <td width="87" align="center"><strong>Reason</strong></td>
      </tr>
		<?php
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $user_id = $row['user_id'];
                    $asset_id = $row['asset_id'];
                    $order_qty = $row['order_qty'];
                    $order_date = $row['order_date'];
                    $order_time = $row['order_time'];
                    $order_info = $row['order_info'];
                    $order_status = $row['order_status'];
                    $order_staReason = $row['order_staReason'];

                    // Fetch the username based on user_id
                    $sql2 = "SELECT username FROM user WHERE user_id='$user_id'";
                    $result2 = $conn->query($sql2);
                    $username = $result2->num_rows > 0 ? $result2->fetch_assoc()['username'] : 'Unknown';
					
					// Fetch the asset_name based on asset_id
                    $sql3 = "SELECT asset_name FROM asset WHERE asset_id='$asset_id'";
                    $result3 = $conn->query($sql3);
                    $asset_name = $result3->num_rows > 0 ? $result3->fetch_assoc()['asset_name'] : 'Unknown';

                    echo "<tr>
                        <td align='center'>{$i}</td>
                        <td align='center'>{$username}</td>
                        <td align='center'>{$asset_name}</td>
                        <td align='center'>{$order_qty}</td>
                        <td align='center'>{$order_date}</td>
                        <td align='center'>{$order_time}</td>
                        <td align='center'>{$order_info}</td>
                        <td align='center'>
                            <form method='post'>
                                <input type='hidden' name='order_id' value='{$row['order_id']}'>
                                <select name='order_status'>
                                    <option value='Pending'" . ($order_status == 'Pending' ? ' selected' : '') . ">Pending</option>
                                    <option value='Accepted'" . ($order_status == 'Accepted' ? ' selected' : '') . ">Accepted</option>
                                    <option value='Rejected'" . ($order_status == 'Rejected' ? ' selected' : '') . ">Rejected</option>
									<option value='Delivered'" . ($order_status == 'Delivered' ? ' selected' : '') . ">Delivered</option>
                                    <option value='Cancelled'" . ($order_status == 'Cancelled' ? ' selected' : '') . ">Cancelled</option>
                                </select>
                        </td>
                        <td align='center'>
                            <select name='order_staReason'>
                                <option value='Order have been Accepted'" . ($order_staReason == 'Order have been Accepted' ? ' selected' : '') . ">Order have been Accepted</option>
                                <option value='Order have been Rejected'" . ($order_staReason == 'Order have been Rejected' ? ' selected' : '') . ">Order have been Rejected</option>
                                <option value='Order have been Cancelled'" . ($order_staReason == 'Order have been Cancelled' ? ' selected' : '') . ">Order have been Cancelled</option>
                                <option value='Order have been Delivered'" . ($order_staReason == 'Order have been Delivered' ? ' selected' : '') . ">Order have been Delivered</option>
                            </select>
                            <input type='submit' value='Update'>
                            </form>
                        </td>
                    </tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='10' align='center'>No orders found.</td></tr>";
            }
            ?>
  </tbody>
    </table>
  
 <p>
    <section class="admin_section" align="center">
    <strong>
        <a href="admin_logout.php">Logout</a>
    </strong>
</section>
  </p> 
</form>  
</main>
</body>
</html>