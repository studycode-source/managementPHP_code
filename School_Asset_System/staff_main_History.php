<!doctype html>
<html>
<head>

<title>Staff-Main menu</title>
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
    <p align="center"><strong> HISTORY LIST</strong></p>
  <p align="center">
	<select name="assetsearch" id="assetsearch">
                <option value="">-- Select --</option>
                <?php
                include('dbschool.php');
                // Using ENUM values directly in the dropdown
                $enum_values = ["Book", "Order"];
                foreach ($enum_values as $value) {
                    echo '<option value="' . $value . '">' . $value . '</option>';
                }
                ?>
            </select>
    <input type="submit" name="search" id="search" value="Search">
  </p>
<table width="90%" height="155" border="1" cellpadding="1" cellspacing="1" align="center">    
	<tbody>
      <tr>
        <td width="51" height="49" align="center"><strong>No</strong></td>
        <td width="270" align="center"><strong> Asset</strong></td>
        <td width="82" align="center"><strong>Date </strong></td>
        <td width="95" align="center"><strong>Time</strong></td>
		<td width="95" align="center"><strong>Info</strong></td>
        <td width="197" align="center"><strong>Status</strong></td>
		<td width="197" align="center"><strong>Book/Order</strong></td>
      </tr>
<?php
include('dbschool.php');
session_start(); // Ensure the session is started to access session variables
$username = $_SESSION['login_user']; // Get the logged-in user's username

if (isset($_POST['search'])) {
    $selection = $_POST['assetsearch']; // Capture the selected value
    if ($selection === "Order") {
        $sql = "SELECT o.*, a.asset_name
                FROM `order` AS o 
                JOIN asset AS a ON o.asset_id = a.asset_id 
                JOIN user AS u ON o.user_id = u.user_id
                WHERE u.username = '$username'"; // Filter by logged-in user's username
        $result = $conn->query($sql);
    } elseif ($selection === "Book") {
        $sql = "SELECT b.*, a.asset_name 
                FROM booking AS b 
                JOIN asset AS a ON b.asset_id = a.asset_id 
                JOIN user AS u ON b.user_id = u.user_id 
                WHERE u.username = '$username'"; // Filter by logged-in user's username
        $result = $conn->query($sql);
    } else {
        // Default case if no selection is made
        $result = null;
    }
} else {
    // Show default if no search
    $result = null;
}

// Check if the result is valid and process the data
if ($result && $result->num_rows > 0) {
    $i = 0;
    while ($rows = $result->fetch_assoc()) {
        $i++;
?>
<tr>
    <td height="168" align="center"><?php echo $i; ?></td>
    <td align="center"><?php echo $rows['asset_name']; ?></td>
    <td align="center"><?php echo isset($rows['booking_date']) ? $rows['booking_date'] : $rows['order_date']; ?></td>
    <td align="center"><?php echo isset($rows['booking_time']) ? $rows['booking_time'] : $rows['order_time']; ?></td>
    <td align="center"><?php echo isset($rows['booking_info']) ? $rows['booking_info'] : $rows['order_info']; ?></td>
    <td align="center"><?php echo isset($rows['booking_status']) ? $rows['booking_status'] : $rows['order_status']; ?></td>
    <td align="center"><?php echo $selection; ?></td> <!-- Displays either "Book" or "Order" -->
</tr>
<?php
    }
} else {
    echo "<tr><td colspan='7' align='center'>No records found</td></tr>";
}
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