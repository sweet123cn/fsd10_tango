<?php 


session_start(); 

$_SESSION = []; 

session_destroy(); 





// redirect to the home page
header("location: index.php");
die();

?>