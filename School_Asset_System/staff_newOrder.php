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

// Fetch user_id based on username
$sql_user = "SELECT user_id FROM user WHERE username = '$username'";
$result_user = $conn->query($sql_user);
$user_row = $result_user->fetch_assoc();
$user_id = $user_row['user_id']; // Now you have the correct user_id

// Initialize variables
$asset_name = '';

if (isset($_POST['submit'])) {
    $order_qty = $conn->real_escape_string($_POST['order_qty']);
    $order_date = $conn->real_escape_string($_POST['order_date']);
    $order_time = $conn->real_escape_string($_POST['order_time']);
    $order_info = $conn->real_escape_string($_POST['order_info']);
    $asset_name = $conn->real_escape_string($_POST['asset_name']); // Get the asset name from input
    $asset_type = $conn->real_escape_string($_POST['asset_type']); // Get the asset type from input

    // Default order status and reason
    $order_status = "Pending";
    $order_staReason = "Awaiting confirmation";

    // Check if the asset exists
    $sql_check = "SELECT asset_id FROM asset WHERE asset_name = '$asset_name'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows == 0) {
        // Asset does not exist, insert it
        $sql_insert_asset = "INSERT INTO asset (asset_name, asset_type, asset_info) 
                             VALUES ('$asset_name', '$asset_type', '$order_info')";
        
        if ($conn->query($sql_insert_asset) === TRUE) {
            $asset_id = $conn->insert_id; // Get the newly created asset_id
            
            // Now insert the order
            $sql_insert = "INSERT INTO `order` (order_qty, order_date, order_time, order_info, user_id, asset_id, order_status, order_staReason) 
                           VALUES ('$order_qty', '$order_date', '$order_time', '$order_info', 
                           '$user_id', '$asset_id', 
                           '$order_status', '$order_staReason')";

            if ($conn->query($sql_insert) === TRUE) {
                echo "New asset created and order placed successfully!";
                header("Location: staff_main_Order.php"); // Redirect after processing
                exit();
            } else {
                echo "Error inserting order: " . $conn->error; // Display error if insertion fails
            }
        } else {
            echo "Error creating asset: " . $conn->error; // Handle asset creation error
        }
    } else {
        $row = $result_check->fetch_assoc();
        $asset_id = $row['asset_id']; // Fetch the asset_id

        // Insert the new order using the user_id fetched from the database
        $sql_insert = "INSERT INTO `order` (order_qty, order_date, order_time, order_info, user_id, asset_id, order_status, order_staReason) 
                       VALUES ('$order_qty', '$order_date', '$order_time', '$order_info', 
                       '$user_id', '$asset_id', 
                       '$order_status', '$order_staReason')";

        if ($conn->query($sql_insert) === TRUE) {
            echo "New order created successfully!";
            header("Location: staff_main_Order.php"); // Redirect after processing
            exit();
        } else {
            echo "Error inserting order: " . $conn->error; // Display error if insertion fails
        }
    }
}
?>


<!doctype html>
<html>
<head>
    <title>New Order</title>
</head>
<body>
    <form action="" id="form1" name="form1" method="post">
    <p align="center"><strong>Order for New Asset</strong></p>
        <table width="509" height="221" border="1" align="center">
          <tbody>
           <tr>
               <td width="180" height="33"><strong>Asset Name</strong></td>
               <td width="313">
                <input type="text" name="asset_name" value="<?php echo htmlspecialchars($asset_name); ?>" required />
               </td>
           </tr>
           <tr>
               <td><strong>Order Quantity</strong></td>
               <td><input type="number" name="order_qty" id="order_qty" min="1" required></td>
           </tr>
           <tr>
               <td><strong>Asset Type</strong></td>
               <td><input type="text" name="asset_type" required /></td>
           </tr>
           <tr>
               <td><strong>Asset Info</strong></td>
               <td><input type="text" name="order_info" required /></td>
           </tr>
           <tr>
               <td><strong>Date</strong></td>
               <td><input type="date" name="order_date" id="date" required></td>
           </tr>
           <tr>
               <td><strong>Time</strong></td>
               <td><input type="time" name="order_time" id="time" required></td>
           </tr>
          </tbody>
        </table>
        <p align="center">
            <input type="submit" name="submit" id="submit" value="Order Now">
            <input type="reset" name="reset" id="reset" value="Reset">
        </p>
    </form>
</body>
</html>
