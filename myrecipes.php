<?php
require "dbConnection.php";


if(isset($_GET["userName"])){
  $sql="SELECT * FROM recipe join users on recipe.user_id = users.user_id WHERE user_name= :userN";
$data["userN"]= $_GET['userName'];
}
$query= $db->prepare($sql);
$query->execute($data);

$pageHead="My Recipes";
$currentNav="showrecipes";
include "sidebar.php";

?>

<section id="recipes">
      <div class="row">
      <?php while($row = $query->fetch()){
  $link="single.php?recipe=".$row['recipe_id'];
  $rating = $row['rating'];
?>
        <div class="col-md-6 portfolio-item">
          <?php if($row['recipe_image_path']!=""){?>
          <a href="<?=$link;?>">
            <img
              class="img-responsive"
              style="height: 250px; width: 100%"
              src="<?=$row['recipe_image_path'];?>"
              alt
            />
          </a>
          <?php if ($userIsLoggedIn){ ?>
					<a href="createRecipe.php?recipe=<?=$row['recipe_id']; ?>">Edit</a> - 
					<a  href="delete.php?recipe=<?=$row['recipe_id']; ?>" onclick="return confirmDelete();">Delete</a>
				<?php } ?>
          <?php } ?>
          <h5>
            <a href="#"><?=$row['recipe_name'];?></a
            >
          </h5>
          <p class="rating">
           <?php for($i=1;$i<=5;$i++){
             if($i<=$rating){
            echo '<i class="bi bi-star-fill checked" style="color: orange"}></i>';
            }else{
            echo '<i class="bi bi-star "></i>';
             }
             };?>  
                  </p>
        </div>
        <?php } ?>
        
        <?php include "includes/footer.php"; ?>     


