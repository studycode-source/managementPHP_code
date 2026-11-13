<?php
include('session.php');

// Ensure the user is logged in and fetch admin details
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'school_asset_management');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get admin ID from the session (to show logged-in admin's profile)
$admin_id = $_SESSION['admin_id'];

// Initialize variables
$name = '';
$email = '';
$mobile = '';
$imguser = '';
$ic = '';

// Fetch admin details for logged-in user
$sql = "SELECT * FROM user WHERE user_id = '$admin_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $name = $admin['username'];
    $email = $admin['user_email'];
    $mobile = $admin['user_mobile'];
    $imguser = $admin['imguser'];
    $ic = $admin['user_IC']; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Register'])) {
    $ic = $_POST['txtIC'];
    $name = $_POST['txtName'];
    $email = $_POST['email2'];
    $mobile = $_POST['txtMobile'];

    // File upload logic
    $pic = '';
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "images/"; // Ensure this folder exists
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $pic = basename($_FILES["photo"]["name"]); // Save only the file name to the database
        }
    }

    // Insert new admin into the database
    $sql_insert = "INSERT INTO user (username, user_IC, user_email, user_mobile, user_password, user_level, imguser) 
                   VALUES ('$name', '$ic', '$email', '$mobile', 'default_password', 'Admin', '$pic')";

    
}

// Handle form submission for deleting admins
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_ids'])) {
    $delete_ids = $_POST['delete_ids'];
    if (!empty($delete_ids)) {
        $delete_ids = implode(",", array_map('intval', $delete_ids)); // Sanitize input (convert to integers)

        $sql_delete = "DELETE FROM user WHERE user_id IN ($delete_ids)"; // Delete all selected admins
        if ($conn->query($sql_delete) === TRUE) {
            // Return JSON success response
            echo json_encode([
                'status' => 'success',
                'message' => 'Selected admins have been deleted successfully.'
            ]);
        } else {
            // Return JSON error response
            echo json_encode([
                'status' => 'error',
                'message' => 'Error deleting admins: ' . $conn->error
            ]);
        }
    } else {
        // Return JSON error if no IDs selected
        echo json_encode([
            'status' => 'error',
            'message' => 'No admin selected for deletion.'
        ]);
    }
    exit(); // Stop further execution after responding to AJAX
}

// Close connection
$conn->close();
?>

<!doctype html>
<html>
<head>
    <title>Admin-Main Home</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <link href="../css/bootstrap-4.4.1.css" rel="stylesheet" type="text/css">
    <link href="config/css/admin.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
    $('#Delete').click(function(event) {
        event.preventDefault();  // Prevent default form submission
        var selected = [];
        $('input[type=checkbox]:checked').each(function() {
            selected.push($(this).val());
        });

        if (selected.length > 0) {
            if (confirm("Are you sure you want to delete the selected admin(s)?")) {
                $.ajax({
                    url: 'admin_main_Home.php', // The deletion logic happens on the same page
                    type: 'POST',
                    data: { delete_ids: selected }, // Pass delete IDs
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        if (response.status == 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('There was an error processing the request: ' + error);
                    }
                });
            }
        } else {
            alert("Please select at least one admin to delete.");
        }
    });
});

    </script>
</head>

<body>
<main class="menu_content">
  <header class="admin_header">
      <h1 align="center"><strong>Welcome to Admin Page</strong></h1>
  </header>
  
  <section class="admin_section"><strong>
      <a href="admin_main_Home.php">Home</a> | <a href="admin_main_Staff.php">Staff</a> | <a href="admin_main_Asset.php">Asset</a> |
      <a href="admin_main_Booking.php">Booking</a> | <a href="admin_main_Order.php">Order</a> | 
      <a href="admin_main_History.php">History</a></strong>
  </section>

  <!-- Profile View -->
  <form id="form1" name="form1" method="post">
      <table width="603" height="289" border="3" align="center">
          <tbody>
              <tr>
                  <th width="167" height="279" scope="col">
                      <img src="images/<?php echo $imguser; ?>" alt="Admin Image" width="200" height="200">
                  </th>
                  <th width="416" scope="col">
                      <p>VIEW YOUR PROFILE</p>
                      <label for="name"><br>Name:</label>
                      <input type="text" name="txtName" id="txtName" value="<?php echo $name; ?>" readonly><br>
                      <label for="email"><br>Email:</label>
                      <input type="email" name="email2" id="email2" value="<?php echo $email; ?>" readonly><br>
                      <label for="mobile"><br>No Tel:</label>
                      <input type="text" name="txtMobile" id="txtMobile" value="<?php echo $mobile; ?>" readonly><br>
                  </th>
              </tr>
          </tbody>
      </table>
  </form>

  <!-- Registration Form -->
  <form id="form2" name="form2" method="post" enctype="multipart/form-data">
    <p align="center"><strong>Register Admin</strong></p>
    <table width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
        <tbody>
            <tr>
                <td width="174" align="left">IC Number</td>
                <td><input type="text" id="txtIC" name="txtIC" required></td>
            </tr>
            <tr>
                <td align="left">Name</td>
                <td><input type="text" id="txtName" name="txtName" required></td>
            </tr>
            <tr>
                <td height="41" align="left">Email</td>
                <td><input type="email" id="email2" name="email2" required></td>
            </tr>
            <tr>
                <td align="left">Number Phone (Mobile)</td>
                <td><input type="text" id="txtMobile" name="txtMobile" required></td>
            </tr>
            <tr>
                <td height="54">Admin Image</td>
                <td><input type="file" name="photo" id="photo"></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="Register" value="Register">
                    <input type="reset" value="Reset">
                </td>
            </tr>
        </tbody>
    </table>
</form>

   <!-- Admin List with Edit and Delete options -->
   <form id="form3" name="form3" method="post">
       <p align="center"><strong>ADMIN LIST</strong></p>
       <table width="759" height="155" border="1" cellpadding="1" cellspacing="1" align="center">
           <tbody>
               <tr>
                   <td width="84" height="72" align="center"><strong>No</strong></td>
                   <td width="81" align="center"><strong>Username</strong></td>
                   <td width="81" align="center"><strong>IC</strong></td>
                   <td width="81" align="center"><strong>Email</strong></td>
                   <td width="81" align="center"><strong>Number Phone</strong></td>
                   <td width="81" align="center"><strong>Delete</strong></td>
                   <td width="87" align="center"><strong>Update</strong></td>
               </tr>
               <?php
               $conn = new mysqli('localhost', 'root', '', 'school_asset_management');
               $sql = "SELECT user_id, username, user_IC, user_email, user_mobile FROM user WHERE user_level = 'Admin'";
               $result = $conn->query($sql);
               if ($result->num_rows > 0) {
                   $no = 1;
                   while ($row = $result->fetch_assoc()) {
                       echo "<tr>";
                       echo "<td align='center'>" . $no . "</td>";
                       echo "<td align='center'>" . $row['username'] . "</td>";
                       echo "<td align='center'>" . $row['user_IC'] . "</td>";
                       echo "<td align='center'>" . $row['user_email'] . "</td>";
                       echo "<td align='center'>" . $row['user_mobile'] . "</td>";
                       echo "<td align='center'><input type='checkbox' name='delete[]' value='" . $row['user_id'] . "'></td>";

                       // Form for editing admin details
                       echo "<td align='center'>";
                       echo "<form action='update_admin.php' method='post'>";
                       echo "<input type='hidden' name='ic' value='" . $row['user_IC'] . "'>"; // Passing the IC via hidden input
                       echo "<input type='submit' name='submit' value='Edit'>";
                       echo "</form>";
                       echo "</td>";

                       echo "</tr>";
                       $no++;
                   }
               } else {
                   echo "<tr><td colspan='7' align='center'>No admin found</td></tr>";
               }
               $conn->close();
               ?>
           </tbody>
       </table>

       <p align="center">
           <input type="submit" name="Delete" id="Delete" value="Delete">
       </p>
   </form>

   <section class="admin_section" align="center">
       <strong><a href="admin_logout.php">Logout</a></strong>
   </section>
</main>
</body>
</html>
