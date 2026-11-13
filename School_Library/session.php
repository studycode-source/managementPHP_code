<?php
   // Start the session
   session_start();

   if(!isset($_SESSION['login_user'])){
      header("location: library_admin_login.php");
      die();
   }
   $login_session = $_SESSION['login_user'];
?>