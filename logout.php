<?php
session_start();
session_destroy(); //end the session
header("Location: loginpage.php"); //redirect home
die();
?>