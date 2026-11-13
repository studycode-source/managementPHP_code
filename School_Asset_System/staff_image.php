<?php
include('dbschool.php');

if (isset($_POST['submit'])) {
    // Define the target directory for file uploads
    $target = "images/";
    $target_file = $target . basename($_FILES['photo']['name']);
    
    // Retrieve file name for storing in the database
    $pic = $_FILES['photo']['name'];

    // Get form data
    $ic = $_POST['txtIC'];
    $name = $_POST['txtName'];
    $email = $_POST['email2'];
    $mobile = $_POST['txtMobile'];
    $user_level = 'Staff';

    // Hash the IC (used as the password)
    $hashed_password = password_hash($ic, PASSWORD_DEFAULT);

    // Insert query with image and other required fields
    $sql = "INSERT INTO user (user_email, username, user_password, user_level, user_IC, user_mobile, imguser) 
            VALUES ('$email', '$name', '$hashed_password', '$user_level', '$ic', '$mobile', '$pic')";

     // Using prepared statements to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("sssssss", $email, $name, $hashed_password, $user_level, $ic, $mobile, $pic);

        // Execute the statement
        if ($stmt->execute()) {
            // Move uploaded file to the target directory
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                echo '<script type="text/javascript">
                          alert("Staff has been recorded successfully.");
                          window.location = "admin_main_Staff.php";
                      </script>';
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>


