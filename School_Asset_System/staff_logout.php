<?php
   session_start();

   if(session_destroy()) {
      header("Location: School_login_staff.php");
   }
?>
