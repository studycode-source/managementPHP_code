<?php
   session_start();

   if(session_destroy()) {
      header("Location: library_admin_login.php");
   }
?>
