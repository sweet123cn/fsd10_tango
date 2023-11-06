<?php

include "dbConnection.php";

// user must be logged in
loginRequired($userIsLoggedIn);

// is there a value in my Get for item
if (!isset($_GET['recipe'])){
	pageNotFound();
}

$data = ['id'=>$_GET['recipe']];

$query = $db->prepare("SELECT * FROM recipe WHERE recipe_id = :id");
$query->execute($data);

$result = $query->fetch();
if (!$result){
	pageNotFound();
}

// delete if exists
$query = $db->prepare("DELETE FROM recipe WHERE recipe_id = :id");
$query->execute($data);

// remove the image if exist
if ($result['recipe_image_path'] != "")
	unlink ($result['recipe_image_path']);



include "includes/header.php";
echo "<div>";
echo "<p>" . $result['title'] . " has been successfully removed" . "</p>";
echo "</div>";
include "includes/footer.php";

?>