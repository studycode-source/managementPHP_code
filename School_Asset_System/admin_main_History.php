<!doctype html>
<html>
<head>
    <title>Admin-Main History</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <link href="../css/bootstrap-4.4.1.css" rel="stylesheet" type="text/css">
    <link href="config/css/admin.css" rel="stylesheet" type="text/css">
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
          <td width="83" height="49" align="center"><strong>Username</strong></td>
          <td width="270" height="49" align="center"><strong>Asset</strong></td>
          <td width="82" height="49" align="center"><strong>Date</strong></td>
          <td width="95" height="49" align="center"><strong>Time</strong></td>
          <td width="95" height="49" align="center"><strong>Info</strong></td>
          <td width="197" height="49" align="center"><strong>Status</strong></td>
          <td width="197" height="49" align="center"><strong>Book/Order</strong></td>
        </tr>
<?php
include('dbschool.php');
$start_from = 0; // Default value, change it according to your pagination logic

if (isset($_POST['search'])) {
    $selection = $_POST['assetsearch']; // Capture the selected value
    if ($selection === "Order") {
        $sql = "SELECT o.*, u.username, a.asset_name
                FROM `order` AS o 
                JOIN user AS u ON o.user_id = u.user_id
				JOIN asset AS a ON o.asset_id = a.asset_id 
                LIMIT $start_from, 3"; // Here $start_from should be defined
        $result = $conn->query($sql);
    } elseif ($selection === "Book") {
        $sql = "SELECT b.*, u.username, a.asset_name 
                FROM booking AS b 
                JOIN user AS u ON b.user_id = u.user_id 
                JOIN asset AS a ON b.asset_id = a.asset_id 
                LIMIT $start_from, 3"; // Here $start_from should be defined
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
    <td height="100" align="center"><?php echo $i; ?></td>
    <td align="center"><?php echo $rows['username']; ?></td>
    <td align="center"><?php echo $selection === "Book" ? $rows['asset_name'] : $rows['asset_name']; ?></td> <!-- Display asset name for both -->
    <td align="center"><?php echo isset($rows['booking_date']) ? $rows['booking_date'] : $rows['order_date']; ?></td>
    <td align="center"><?php echo isset($rows['booking_time']) ? $rows['booking_time'] : $rows['order_time']; ?></td>
    <td align="center"><?php echo isset($rows['booking_info']) ? $rows['booking_info'] : $rows['order_info']; ?></td>
    <td align="center"><?php echo isset($rows['booking_status']) ? $rows['booking_status'] : $rows['order_status']; ?></td>
    <td align="center"><?php echo $selection; ?></td> <!-- Displays either "Book" or "Order" -->
</tr>
<?php
    }
} else {
    echo "<tr><td colspan='8' align='center'>No records found</td></tr>";
}
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
