<?php 

$currentNav = "";

function pageNotFound(){
	global $userIsLoggedIn; 
	include "header.php";
	echo "Page not found";
	include "footer.php";
	die();
}


function validateIsEmptyData($array, $key){


	if (!array_key_exists($key, $array) || $array[$key] ==  ""){
		return true; 
	} else 
	return false;

}



function loginRequired($loginFlag){
	
	$loginFlag = $loginFlag ?? false;
	if ($loginFlag == false){
		header("location: login.php");
		die();
	}
}
?>