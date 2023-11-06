<?php

require "dbConnection.php";


$errorMessages = "";
$recipeId = "";
$txtTitle = "";
$txtDescription="";
$txtIngredients = "";
$fileImage = "";
$txtInstruction = "";
$selMeals = "";
$selCuisine="";
$userId="";
$selIngredients=[];
if(array_key_exists('recipe',$_GET)){
  $query=$db->prepare("SELECT * FROM recipe WHERE recipe_id = :id");
  $query->execute(['id'=>$_GET['recipe']]);
  $data = $query->fetch();

	if (!$data){ 
		pageNotFound();
	}
  $recipeId = $data['recipe_id'];
$txtTitle = $data['recipe_name'];
$txtIngredients = $data['material_description'];
$txtDescription=$data['description'];
$fileImage = $data['recipe_image_path'];
$txtInstruction =$data['step_instruction'];
$userId=$data['user_id'];
$selMeals = $data['meal_id'];
$selCuisine=$data['cuisine_id'];
$query1=$db->prepare("DELETE FROM recipe_ingredient WHERE recipe_id = :id");
$query1->execute(['id'=>$_GET['recipe']]);
}
if ($_SERVER['REQUEST_METHOD'] == "POST"){
	// form was submitted
  // echo "<pre>";
  // print_r($_POST);
  // echo "</pre>";
	// validating the form
	if (validateIsEmptyData($_POST, 'txtTitle')) $errorMessages .= "Title is required <br>";
	else $txtTitle = $_POST['txtTitle'];

	if (validateIsEmptyData($_POST, 'txtIngredients')) $errorMessages .= "Ingredients is required <br>";
	else $txtIngredients = $_POST['txtIngredients'];
  if (validateIsEmptyData($_POST, 'txtDescription')) $errorMessages .= "Ingredients is required <br>";
	else $txtDescription = $_POST['txtDescription'];

	if (validateIsEmptyData($_POST, 'txtInstruction')) $errorMessages .= "Instruction is required <br>";
	else $txtInstruction = $_POST['txtInstruction'];

	if (validateIsEmptyData($_POST, 'selMeals')) $errorMessages .= "Meals is required <br>";
	else $selMeals = $_POST['selMeals'];

  if (validateIsEmptyData($_POST, 'selCuisine')) $errorMessages .= "Cuisine is required <br>";
	else $selCuisine = $_POST['selCuisine'];

  if (validateIsEmptyData($_POST, 'selIngredients')) $errorMessages .= "select Ingredeint is required <br>";

	$fileImage = $_POST['oldImage'] ?? ""; 
	$recipeId = $_POST['recipeId'] ?? ""; 
  $userId = $_SESSION['user_id'];
  if ($errorMessages == ""){
		
		if ($_FILES['fileImage']['error'] == 0){
			$sourceFile = $_FILES['fileImage']['tmp_name'];
			$destinationFile = "upload/" . $_FILES['fileImage']['name'];
			if (move_uploaded_file($sourceFile, $destinationFile)){
				if ($fileImage  != "" && $fileImage != $destinationFile){
					unlink($fileImage); //DELETE FROM THE SERVER
				}
				$fileImage = $destinationFile;
			} else {
				// file has NOT been moved
			}
	
		} 
    $data = [
			"title" => $txtTitle, 
			"ingredients" => $txtIngredients,
      "description"=>$txtDescription,
			"instruction" => $txtInstruction,
			"imagepath" => $fileImage,
      "userid" =>$userId,
			"meal_id" => $selMeals,
      "cuisine_id"=>$selCuisine,
			"utime" => date("Y-m-d")
		];


    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";
    if ($recipeId == ""){
			// no item id was found = add new row to the database
			$sql = "INSERT INTO recipe (recipe_name, material_description,description, step_instruction, user_id, recipe_image_path, created_datetime, meal_id,cuisine_id) VALUES (:title, :ingredients, :description,:instruction,:userid, :imagepath, :utime, :meal_id, :cuisine_id);";
		}else{
			// update existing row
			$sql = "UPDATE recipe SET recipe_name = :title, description=:description, material_description = :ingredients, step_instruction = :instruction,user_id= :userid, meal_id = :meal_id, cuisine_id=:cuisine_id, recipe_image_path = :imagepath, updated_datetime = :utime WHERE recipe_id = :pid";

			$data['pid'] = $recipeId;
			
		}
    $query = $db->prepare($sql);			
		$query->execute($data);

    if($recipeId=="") $recipeId = $db->lastInsertId();

  if (isset($_POST['selIngredients'])){
    foreach($_POST['selIngredients'] as $ingredient){
      $sql="INSERT INTO recipe_ingredient(recipe_id,ingredient_id) VALUES ($recipeId,$ingredient);";
      $queryinsert = $db->query($sql);
    }
  }
  $selIngredients=$_POST['selIngredients'];

header("location: recipe_detail.php?recipe_id={$recipeId}");


}
}
$pageHead = ($recipeId=="") ? "Add New recipe" : "Update a recipe";
$currentNav = "add";
include "sidebar.php";
?>
 
     <div class="row">
      <form class="col-md-12 " method="POST" enctype="multipart/form-data" action="createRecipe.php">
      <input type="hidden" name="recipeId" value="<?=$recipeId; ?>" >
		<input type="hidden" name="oldImage" value="<?= $fileImage; ?>" >
    <input type="hidden" name="userId" value="<?=$userId; ?>" >
    <p><?=$errorMessages ?></p>
      <div class="form-group">
        <label for="txtTitle" class="control-label">Title</label>
        <input id="txtTitle" name="txtTitle" type="text" required="required" class="form-control" value="<?=$txtTitle; ?>">
      </div>
      <div class="form-group">
        <label for="txtDescription" class="control-label">Description</label>
        <textarea id="txtDescription" name="txtDescription" cols="40" rows="1" required="required" class="form-control"><?=$txtDescription; ?></textarea>
      </div>
      <div class="form-group">
        <label for="txtIngredients" class="control-label">Materials</label>
        <textarea id="txtIngredients" name="txtIngredients" cols="40" rows="2" required="required" class="form-control"><?=$txtIngredients; ?></textarea>
      </div>
      <div class="form-group">
        <label for="txtInstruction" class="control-label">instruction</label>
        <textarea id="txtInstruction" name="txtInstruction" cols="40" rows="5" required="required" class="form-control"><?=$txtInstruction; ?></textarea>
      </div>
      <div class="form-group">
        <label for="fileImage" class="control-label">Image</label>
        <input id="fileImage" name="fileImage" type="file" class="form-control">
      </div>
      <div class="form-group">
        <label for="selMeals" class="control-label">Meals</label>
        <select id="selMeals" name="selMeals" required="required" class="select form-control">
          <option value="">-----</option>
          <?php foreach($allMeals as $meal_id => $meal_name){ 
					$selected = ($meal_id == $selMeals) ? "selected" : "";
					?>
					<option value="<?=$meal_id; ?>" <?=$selected; ?>><?=$meal_name; ?></option>
				<?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label for="selCuisine" class="control-label">cuisine</label>
        <select id="selCuisine" name="selCuisine" required="required" class="select form-control">
          <option value="">-----</option>
          <?php foreach($allCuisines as $cuisine_id => $cuisine_type){ 
					$selected = ($cuisine_id == $selCuisine) ? "selected" : "";
					?>
					<option value="<?=$cuisine_id; ?>" <?=$selected; ?>><?=$cuisine_type; ?></option>
				<?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label for="selIngredient" class="control-label">Ingredients</label>
        <select id="selIngredient" name="selIngredients[]" multiple size="3" required="required" class="select form-control">
          <option value="">-----</option>
          <?php foreach($allIngredients as $ingredient_id => $ingredient_name){ 
					$selected = (in_array($ingredient_id,$selIngredients)) ? "selected" : "";
					?>
					<option value="<?=$ingredient_id; ?>"<?=$selected; ?> ><?=$ingredient_name; ?></option>
				<?php } ?>
        </select>
      </div>
      <div class="form-group mt-4 text-center">
        <button name="btnSubtmi" type="submit" class="btn btn-warning">Submit</button>
      </div>
      </form>
      </div>  
        </div>
      </div>
    </div>   
    <?php include "includes/footer.php"; ?>  