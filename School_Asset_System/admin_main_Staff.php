<!doctype html>
<html>
<head>
    <title>Admin-Main menu</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <link href="../css/bootstrap-4.4.1.css" rel="stylesheet" type="text/css">
    <link href="config/css/admin.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>
<main class="menu_content">
    <header class="admin_header">
        <h1 align="center"><strong>Welcome to admin page</strong></h1>
    </header>
    
    <section class="admin_section">
        <strong><a href="admin_main_Home.php">Home</a> | 
        <a href="admin_main_Staff.php">Staff</a> | 
        <a href="admin_main_Asset.php">Asset</a> | 
        <a href="admin_main_Booking.php">Booking</a> | 
        <a href="admin_main_Order.php">Order</a> | 
        <a href="admin_main_History.php">History</a></strong>
    </section>
    
    <!-- Staff Registration and Edit Section -->
    <form id="form2" name="form2" method="post" enctype="multipart/form-data">
        <p class="PSS_section" align="center"><strong>Register Staff</strong></p>
        <table width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
            <tbody>
                <tr>
                    <td width="174" align="left">IC Number</td>
                    <td><input type="text" id="txtIC" name="txtIC" value="<?php echo isset($ic) ? $ic : ''; ?>" required></td>
                </tr>
                <tr>
                    <td align="left">Name</td>
                    <td><input type="text" id="txtName" name="txtName" value="<?php echo isset($name) ? $name : ''; ?>" required></td>
                </tr>
                <tr>
                    <td height="41" align="left">Email</td>
                    <td><input type="email" id="email2" name="email2" value="<?php echo isset($email) ? $email : ''; ?>" required></td>
                </tr>
                <tr>
                    <td align="left">Number Phone (Mobile)</td>
                    <td><input type="text" id="txtMobile" name="txtMobile" value="<?php echo isset($mobile) ? $mobile : ''; ?>" required></td>
                </tr>
                <tr>
                    <td height="54">Staff Image</td>
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

    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'school_asset_management');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
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

    // Insert new staff into the database
    $sql_insert = "INSERT INTO user (username, user_IC, user_email, user_mobile, user_password, user_level, imguser) 
                   VALUES ('$name', '$ic', '$email', '$mobile', '$ic', 'Staff', '$pic')";
	
	if ($conn->query($sql_insert) === TRUE) {
    echo "New staff registered successfully.";
} else {
    echo "Error: " . $sql_insert . "<br>" . $conn->error;
}

        
    }
	
	
// Close connection
$conn->close();
?>

    <!-- Staff List Section -->
    <form id="form3" name="form3" method="post" action="delete_staff.php">
        <p align="center"><strong>STAFF LIST</strong></p>
        <table width="90%" height="155" border="1" cellpadding="1" cellspacing="1" align="center">
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
               $sql = "SELECT user_id, username, user_IC, user_email, user_mobile FROM user WHERE user_level = 'Staff'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td <td width='84' height='72' align='center'>" . $no . "</td>";
                        echo "<td <td width='84' height='72' align='center'>" . $row['username'] . "</td>";
                        echo "<td <td width='84' height='72' align='center'>" . $row['user_IC'] . "</td>";
                        echo "<td <td width='84' height='72' align='center'>" . $row['user_email'] . "</td>";
                        echo "<td <td width='84' height='72' align='center'>" . $row['user_mobile'] . "</td>";
                        echo "<td align='center'><input type='checkbox' name='delete_ids[]' value='" . $row['user_id'] . "'></td>";
                        // Form for editing staff details
                       echo "<td align='center'>";
                       echo "<form action='update_staff.php' method='post'>";
                       echo "<input type='hidden' name='ic' value='" . $row['user_IC'] . "'>"; // Passing the IC via hidden input
                       echo "<input type='submit' name='submit' value='Edit'>";
                       echo "</form>";
                       echo "</td>";

                       echo "</tr>";
                       $no++;
                   }
               } else {
                   echo "<tr><td colspan='7' align='center'>No staff found</td></tr>";
               }
               $conn->close();
               ?>
            </tbody>
        </table>
        <p align="center">
            <input type="submit" name="Delete" id="Delete" value="Delete">
        </p>
    </form>

    <p>
        <section class="admin_section" align="center">
            <strong><a href="School_login.php">Logout</a></strong>
        </section>
    </p> 
</main>
</body>
</html>
