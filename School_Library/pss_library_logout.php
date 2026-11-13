<?php
   session_start();

   if(session_destroy()) {
      header("Location: library_pss_login.php");
   }
?>
