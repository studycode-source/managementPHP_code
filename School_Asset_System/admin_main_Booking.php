<?php
include('dbschool.php');

// Start the session
session_start();

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the booking ID from the form submission
    $booking_id = $_POST['booking_id'];
    $booking_status = $_POST['booking_status'];
    $booking_staReason = $_POST['booking_staReason'];
    
    // Update the booking status and reason in the database
    $sql_update = "UPDATE booking SET booking_status = '$booking_status', booking_staReason = '$booking_staReason' WHERE booking_id = '$booking_id'";
    if ($conn->query($sql_update) === TRUE) {
        echo "Booking updated successfully!";
    } else {
        echo "Error updating booking: " . $conn->error;
    }
}

// Fetch all booking information
$sql = "SELECT * FROM booking";
$result = $conn->query($sql);

?>

<!doctype html>
<html>
<head>

<title>Admin-Main Booking</title>
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
  </form>
	<p align="center"><strong> BOOKING LIST</strong></p>
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
        <td width="87" align="center"><strong>Status</strong></td>
		<td width="81" align="center"><strong>Reason</strong></td>
      </tr>
		     <?php
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $user_id = $row['user_id'];
                    $asset_id = $row['asset_id'];
                    $booking_qty = $row['booking_qty'];
                    $booking_date = $row['booking_date'];
                    $booking_time = $row['booking_time'];
                    $booking_info = $row['booking_info'];
                    $booking_status = $row['booking_status'];
                    $booking_staReason = $row['booking_staReason'];

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
                        <td align='center'>{$booking_qty}</td>
                        <td align='center'>{$booking_date}</td>
                        <td align='center'>{$booking_time}</td>
                        <td align='center'>{$booking_info}</td>
                        <td align='center'>
                            <form method='post'>
                                <input type='hidden' name='booking_id' value='{$row['booking_id']}'>
                                <select name='booking_status'>
                                    <option value='Pending'" . ($booking_status == 'Pending' ? ' selected' : '') . ">Pending</option>
                                    <option value='Accepted'" . ($booking_status == 'Accepted' ? ' selected' : '') . ">Accepted</option>
                                    <option value='Rejected'" . ($booking_status == 'Rejected' ? ' selected' : '') . ">Rejected</option>
                                    <option value='Cancelled'" . ($booking_status == 'Cancelled' ? ' selected' : '') . ">Cancelled</option>
                                </select>
                        </td>
                        <td align='center'>
							<table>
								<tr>
									<td>
										<select name='booking_staReason'>
											<option value='other users have booked'" . ($booking_staReason == 'other users have booked' ? ' selected' : '') . ">Other users have booked</option>
											<option value='Assets are damaged'" . ($booking_staReason == 'Assets are damaged' ? ' selected' : '') . ">Assets are damaged</option>
											<option value='Assets are not enough'" . ($booking_staReason == 'Assets are not enough' ? ' selected' : '') . ">Assets are not enough</option>
											<option value='Asset is in use'" . ($booking_staReason == 'Asset is in use' ? ' selected' : '') . ">Asset is in use</option>
											<option value='Asset Accepted'" . ($booking_staReason == 'Asset is in use' ? ' selected' : '') . ">Asset Accepted</option>
											<option value='Booking Cancelled'" . ($booking_staReason == 'Asset is in use' ? ' selected' : '') . ">Booking Cancelled</option>
										</select>
									</td>
									<td>
										<input type='submit' value='Update'>
									</td>
								</tr>
							</table>
                            
                            
                            </form>
                        </td>
                    </tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='10' align='center'>No bookings found.</td></tr>";
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
  
</main>
</body>
</html>