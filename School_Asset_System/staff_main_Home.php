<?php
include('session_staff.php');

// Ensure the user is logged in and fetch staff details
if (!isset($_SESSION['staff_id'])) {
    header("Location: School_login_staff.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'school_asset_management');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get staff ID from the session (to show logged-in staff's profile)
$staff_id = $_SESSION['staff_id'];

// Initialize variables
$name = '';
$email = '';
$mobile = '';
$imguser = '';
$ic = '';

// Fetch staff details for logged-in user
$sql = "SELECT * FROM user WHERE user_id = '$staff_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $staff = $result->fetch_assoc();
    $name = $staff['username'];
    $email = $staff['user_email'];
    $mobile = $staff['user_mobile'];
    $imguser = $staff['imguser'];
    $ic = $staff['user_IC']; 
}



// Close the database connection at the end
$conn->close();
?>


<!doctype html>
<html>
<head>

<title>Staff-Main Home</title>
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
    <h1 align="center"><strong>Welcome to staff page</strong></h1>
  </header>
  
  <section class="staff_section">
    <strong><a href="staff_main_Home.php">Home</a> | <a href="staff_main_Asset.php">Asset</a> | <a href="staff_main_Booking.php">Booking</a> | <a href="staff_main_Order.php">Order</a> | <a href="staff_main_History.php">History</a></strong>
  </section>
  
  <form id="form1" name="form1">
    <table width="603" height="289" border="3" align="center">
      <tbody>
        <tr>
          <th width="167" height="279" scope="col">
              <img src="images/<?php echo $imguser; ?>" alt="Staff Image" width="200" height="200">
          </th>
          <th width="416" scope="col">
              <p>VIEW YOUR PROFILE</p>
              <label for="name">Name:</label>
              <input type="text" name="txtName" id="txtName" value="<?php echo $name; ?>" readonly><br>
              
              <label for="email">Email:</label>
              <input type="email" name="email2" id="email2" value="<?php echo $email; ?>" readonly><br>
              
              <label for="mobile">No Tel:</label>
              <input type="text" name="txtMobile" id="txtMobile" value="<?php echo $mobile; ?>" readonly><br>
          </th>
        </tr>
      </tbody>
    </table>
  </form>
  <p>
  <section class="staff_section" align="center">
    <strong><a href="staff_logout.php">Logout</a></strong>
  </section></p>
  
</main>
</body>
</html>
