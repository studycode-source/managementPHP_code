<?php
   include("config.php");
   session_start();
   $error='';
   if($_SERVER["REQUEST_METHOD"] == "POST") {
   
      // username and password sent from form 
      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 

      $sql = "SELECT * FROM user WHERE username = '$myusername' and user_IC = '$mypassword' and user_level='staff'";

      $result = mysqli_query($db,$sql);      
      $row = mysqli_num_rows($result);      
      $count = mysqli_num_rows($result);

      if($count == 2) {
	  
         // session_register("myusername");
         $_SESSION['login_user'] = $myusername;
         header("location: staff_main_Home.php");
      } else {
         $error = "Your Login Name or Password is invalid";
      }
	   
	   // After successful login
	   if($count == 1) {
    $staff = mysqli_fetch_assoc($result); // Fetch the staff details
    $_SESSION['login_user'] = $staff['username']; // Store username in session
    $_SESSION['staff_id'] = $staff['user_id']; // Store user ID in session for future use
    header("location: staff_main_Home.php");
    exit();
}
   }
?>

<!doctype html>
<html>
<head>
    <title>Login - School Asset Management System</title>
	<link href="config/css/staff.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor = "#FFFFFF">
   <div align = "center">
      <div style = "width:300px; border: solid 1px #333333; " align = "left">
         <div style = "background-color:#333333; color:cornflowerblue; padding:3px;" align="center"><b>Staff Login</b></div>
         <div style = "margin:30px">
              <form action="" method="POST" align="justify">
                    Username: <br>
                    <input type="text" name="username" placeholder="Enter Username" class = "box"><br><br>

                    Password: <br>
                    <input type="password" name="password" placeholder="Enter Password" class = "box"><br><br>
                    <br>
                    <input type="submit" name="submit" id="submit" value="Login">
				  <br>
                    <p><a href="index.php">Back</a></p>
				  <p>&nbsp;</p>
              </form>
            <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
         </div>
      </div>
   </div>
</body>
</html>