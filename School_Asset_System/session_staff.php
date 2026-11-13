<?php
   // Start the session
   session_start();

   if(!isset($_SESSION['login_user'])){
      header("location: School_login_staff.php");
      die();
   }
   $login_session = $_SESSION['login_user'];
?>