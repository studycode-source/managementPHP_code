<?php
include('dbschool.php');
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['login_user'])) {
    header("Location: School_login_staff.php"); // Redirect to login if not logged in
    exit();
}

// Get the logged-in username
$username = $_SESSION['login_user'];

if (isset($_POST['submit'])) {
    $booking_qty = $_POST['booking_qty'];
    $booking_date = $_POST['date'];
    $booking_time = $_POST['time'];
    $asset_info = $_POST['assetinfo'];
    $asset_name = $_POST['assetname'];
    
    // Set default status and reason
    $booking_status = "Pending"; // Default status
    $booking_staReason = "Awaiting confirmation"; // Default reason

    // Check if a booking already exists for the same user, asset, date, and time
$sql_check = "SELECT * FROM booking 
              WHERE user_id = (SELECT user_id FROM user WHERE username = '$username') 
              AND asset_id = (SELECT asset_id FROM asset WHERE asset_name = '$asset_name') 
              AND booking_date = '$booking_date'
              AND booking_time = '$booking_time'";

$result_check = $conn->query($sql_check);

if ($result_check->num_rows > 0) {
    echo "You have already booked this asset for the selected date and time.";
} else {
    // Try inserting the new booking
    try {
        $sql_insert = "INSERT INTO booking (booking_qty, booking_date, booking_time, booking_info, user_id, asset_id, booking_status, booking_staReason) 
                       VALUES ('$booking_qty', '$booking_date', '$booking_time', '$asset_info', 
                       (SELECT user_id FROM user WHERE username = '$username'), 
                       (SELECT asset_id FROM asset WHERE asset_name = '$asset_name'),
                       '$booking_status',
                       '$booking_staReason')";

        if ($conn->query($sql_insert) === TRUE) {
            echo "Booking created successfully!";
        } else {
            throw new Exception("Error: " . $conn->error);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}


}

// Handle booking cancellation
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $sql_cancel = "UPDATE booking SET booking_status = 'Cancelled' WHERE booking_id = '$booking_id'";
    if ($conn->query($sql_cancel) === TRUE) {
        echo "Booking cancelled successfully!";
    } else {
        echo "Error cancelling booking: " . $conn->error;
    }
}

// Fetch staff bookings
$sql_bookings = "SELECT * FROM booking WHERE user_id = (SELECT user_id FROM user WHERE username = '$username')";
$result_bookings = $conn->query($sql_bookings);
?>

<!doctype html>
<html>
<head>
    <title>Staff-Main Booking</title>
    <link rel="stylesheet" type="text/css" href="../css/staff.css">
    <link href="../css/bootstrap-4.4.1.css" rel="stylesheet" type="text/css">
    <link href="config/css/staff.css" rel="stylesheet" type="text/css">
    <script>var __adobewebfontsappname__="dreamweaver"</script>
    <script src="http://use.edgefonts.net/allerta:n4:default.js" type="text/javascript"></script>
</head>

<body>
<main class="menu_content">
    <header class="staff_header">
        <h1 align="center"><strong>Welcome to staff page</strong></h1>
    </header>

    <section class="staff_section">
        <strong>
            <a href="staff_main_Home.php">Home</a> | 
            <a href="staff_main_Asset.php">Asset</a> | 
            <a href="staff_main_Booking.php">Booking</a> | 
            <a href="staff_main_Order.php">Order</a> | 
            <a href="staff_main_History.php">History</a>
        </strong>
    </section>
  <?php
    // Check if asset_name is passed via GET request
    if (isset($_GET['asset_name'])) {
        // Fetch asset details
        $asset_name = $_GET['asset_name'];
        $sql = "SELECT * FROM asset WHERE asset_name = '$asset_name'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $rows = $result->fetch_assoc();
    ?>
            <form action="staff_main_Booking.php" id="form1" name="form1" method="post">
                <p align="center"><strong>Booking for Asset</strong></p>
                <table width="509" height="221" border="1" align="center">
                    <tbody>
                        <tr>
                            <td width="180" height="33"><strong>Asset Name</strong></td>
                            <td width="313">
                                <label><?php echo $rows['asset_name']; ?><input type="hidden" name="assetname" value="<?php echo $rows['asset_name']; ?>"/></label>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Asset Quantity</strong></td>
                            <td><?php echo $rows['asset_qty']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Booking Quantity</strong></td>
                			<td><input type="number" name="booking_qty" id="booking_qty" min="1" max="<?php echo $rows['asset_qty']; ?>" required></td>
                        </tr>
                        <tr>
                            <td><strong>Asset Type</strong></td>
                            <td><?php echo $rows['asset_type']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Asset Info</strong></td>
                            <td><?php echo $rows['asset_info']; ?><input type="hidden" name="assetinfo" value="<?php echo $rows['asset_info']; ?>"/></td>
							
                        </tr>
                        <tr>
                            <td><strong>Date</strong></td>
                            <td><input type="date" name="date" id="date" required></td>
                        </tr>
                        <tr>
                            <td><strong>Time</strong></td>
                            <td><input type="time" name="time" id="time" required></td>
                        </tr>
                    </tbody>
                </table>
                <p align="center">
                    <input type="submit" name="submit" id="submit" value="Book Now">
                    <input type="reset" name="reset" id="reset" value="Reset">
                </p>
            </form>
     <?php
        } else {
            echo "Asset not found.";
        }
    }
    ?>

            
    
    
    <p align="center"><strong>BOOKING LIST</strong></p>
  <table width="90%" height="155" border="1" cellpadding="1" cellspacing="1" align="center">        
	  <tbody>
            <tr>
                <td width="84" height="72" align="center"><strong>No</strong></td>
                <td width="81" align="center"><strong>Asset</strong></td>
				<td width="81" align="center"><strong>Asset Quantity</strong></td>
                <td width="81" align="center"><strong>Date</strong></td>
                <td width="81" align="center"><strong>Time</strong></td>
                <td width="81" align="center"><strong>Info</strong></td>
                <td width="81" align="center"><strong>Status</strong></td>
                <td width="81" align="center"><strong>Reason</strong></td>
				<td width="81" align="center"><strong></strong></td>
            </tr>

           <?php
            if ($result_bookings->num_rows > 0) {
                $i = 1;
                while ($row = $result_bookings->fetch_assoc()) {
                    // Fetching the asset name based on asset_id
                    $asset_id = $row['asset_id'];
                    $sql_asset = "SELECT asset_name FROM asset WHERE asset_id = '$asset_id'";
                    $result_asset = $conn->query($sql_asset);

                    if ($result_asset->num_rows > 0) {
                        $asset_name = $result_asset->fetch_assoc()['asset_name'];
                    } else {
                        $asset_name = 'Unknown Asset';
                    }

                    // Check if booking status is not already cancelled
                    if ($row['booking_status'] != 'Cancelled') {
                        echo "<tr align='center'>
                            <td height='168'>{$i}</td>
                            <td>{$asset_name}</td>
                            <td>{$row['booking_qty']}</td>
                            <td>{$row['booking_date']}</td>
                            <td>{$row['booking_time']}</td>
                            <td>{$row['booking_info']}</td>
                            <td>{$row['booking_status']}</td>
							<td>{$row['booking_staReason']}</td>
                            <td>
                                <form method='post'>
                                    <input type='hidden' name='booking_id' value='{$row['booking_id']}'>
                                    <input type='submit' name='cancel_booking' value='Cancel'>
                                </form>
                            </td>
                        </tr>";
                    } else {
                        echo "<tr align='center'>
                            <td height='168'>{$i}</td>
                            <td>{$asset_name}</td>
                            <td>{$row['booking_qty']}</td>
                            <td>{$row['booking_date']}</td>
                            <td>{$row['booking_time']}</td>
                            <td>{$row['booking_info']}</td>
                            <td>{$row['booking_status']}</td>
                            <td>Cancelled</td>
                        </tr>";
                    }

                    $i++;
                }
            } else {
                echo "<tr><td colspan='8' align='center'>No bookings found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
<p>
    <section class="staff_section" align="center">
        <strong><a href="staff_logout.php">Logout</a></strong>
    </section>
	</p>
</main>

<?php
$conn->close();
?>
</body>
</html>	
