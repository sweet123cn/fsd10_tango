<?php
require "dbConnection.php";


if(isset($_GET["userName"])){
  $sql="SELECT * FROM recipe inner join favorite_recipe on recipe.recipe_id = favorite_recipe.recipe_id inner join users on users.user_id=favorite_recipe.user_id WHERE user_name= :userN";
$data["userN"]= $_GET['userName'];
}
$query= $db->prepare($sql);
$query->execute($data);

$pageHead="My favorites";
$currentNav="likerecipes";
include "sidebar.php";
?>

<section id="recipes">
      <div class="row">
      <?php while($row = $query->fetch()){
  $link="recipe_detail.php?recipe=".$row['recipe_id'];
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
          <?php } ?>
          <h5>
            <a href="<?=$link;?>"><?=$row['recipe_name'];?></a
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
        
      </div>
    </section>
    <?php include "includes/footer.php"; ?>  
