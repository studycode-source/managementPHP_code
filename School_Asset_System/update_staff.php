<?php
include('dbschool.php');

// Check if the 'ic' parameter is set in the POST request
if (isset($_POST['ic'])) {
    $ic = $_POST['ic'];
} else {
    echo "Error: IC not provided!";
    exit();
}

// Fetch the admin details using the IC number
$sql = "SELECT * FROM user WHERE user_IC = '$ic'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $rows = $result->fetch_assoc(); // Fetch the row
    $name = $rows['username'];
    $email = $rows['user_email'];
    $mobile = $rows['user_mobile'];
    $imguser = $rows['imguser']; // Pre-populate with current image if needed
} else {
    echo "No staff found with this IC.";
    exit();
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Update staff</title>
</head>

<body>
    <form id="form2" name="form2" method="post" enctype="multipart/form-data">
        <p align="center"><strong>Update staff</strong></p>
        <table width="759" height="218" border="1" cellpadding="1" cellspacing="1" align="center">
            <tbody>
                <tr>
                    <td width="174" align="left">IC Number</td>
                    <td>
                        <input type="text" id="txtIC" name="ic" value="<?php echo $ic; ?>" required readonly>
                    </td>
                </tr>
                <tr>
                    <td align="left">Name</td>
                    <td><input type="text" id="txtName" name="txtName" value="<?php echo $name; ?>" required></td>
                </tr>
                <tr>
                    <td height="41" align="left">Email</td>
                    <td><input type="email" id="email2" name="email2" value="<?php echo $email; ?>" required></td>
                </tr>
                <tr>
                    <td align="left">Number Phone (Mobile)</td>
                    <td><input type="text" id="txtMobile" name="txtMobile" value="<?php echo $mobile; ?>" required></td>
                </tr>
                <tr>
                    <td height="54">staff Image</td>
                    <td><input type="file" name="photo" id="photo"></td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" name="SaveChanges" value="Save Changes">
                        <input type="reset" value="Reset">
                    </td>
                </tr>
				<tr align="center"><td ><strong><a href="admin_main_Staff.php">Back</a></strong></td></tr>
            </tbody>
        </table>
    </form>

    <?php
    if (isset($_POST['SaveChanges'])) {
        // Update existing staff details
        $ic = $_POST['ic']; // Now it correctly gets the IC number
        $name = $_POST['txtName'];
        $email = $_POST['email2'];
        $mobile = $_POST['txtMobile'];

        // File upload logic (no validation checks, just upload and save to DB)
        $pic = $imguser; // Default to existing image if not updated
        if (!empty($_FILES['photo']['name'])) {
            $target_dir = "images/"; // Ensure this folder exists
            $target_file = $target_dir . basename($_FILES["photo"]["name"]);
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $pic = basename($_FILES["photo"]["name"]); // Save only the file name to the database
            }
        }

        // Update query to save the new data
        $sql1 = "UPDATE user SET user_email = '$email', username = '$name', user_mobile = '$mobile', imguser = '$pic' WHERE user_IC = '$ic'";

        if ($conn->query($sql1) === TRUE) {
            echo '<script type="text/javascript">
                alert("Staff information updated.");
                window.location = "admin_main_Staff.php";
                </script>';
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }

    $conn->close();
    ?>
</body>
</html>
