<?php
include('dbschool.php');

if (isset($_POST['submit'])) {
    $target = "images/";
    $target = $target . basename($_FILES['photo']['name']);
    
    $asset_name = $_POST['assetname'];
    $asset_type = $_POST['asset_type1']; // Getting asset type from dropdown
    $asset_qty = $_POST['assetqty'];
    $asset_info = $_POST['assetinfo'];
    $pic = $_FILES['photo']['name'];
    
    // Make sure the asset_name and other fields are not empty
    if (!empty($asset_name) && !empty($asset_type) && !empty($asset_qty) && !empty($pic)) {
        // Insert asset details into the asset table
        $sql = "INSERT INTO asset (asset_name, asset_type, asset_qty, asset_info, imgasset) 
                VALUES ('$asset_name', '$asset_type', '$asset_qty', '$asset_info', '$pic')";
    
        if ($conn->query($sql) === TRUE) {
            move_uploaded_file($_FILES['photo']['tmp_name'], $target);
            echo '<script type="text/javascript">
                  alert("Asset has been recorded.");
                  window.location = "admin_main_Asset.php";
                  </script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo 'All fields are required!';
    }
}

$conn->close();
?>
