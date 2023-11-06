
    <?php
    
    include "includes/header.php";
   ?>

    <div class="container rounded bg-white mt-5 mb-5">
      <div class="row sidebar">
        <div class="col-md-3 border-right">
          <div
            class="d-flex flex-column align-items-center text-center p-3 py-5"
          >
            <img
              class="rounded-circle mt-5"
              width="150px"
              src="<?=$_SESSION['user_image'];?>"
            >
            <span class="font-weight-bold"><?=$_SESSION['userName'];?></span>
            <span class="text-black-50"><?=$_SESSION['user_email'];?></span>
          </div>
          <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
              <a
                href="myrecipes.php?userName=<?=$_SESSION['userName'];?>"
                class="nav-link bg-warning text-center border-left border-right rounded-0 <?php if ($currentNav == "showrecipes") echo "active"; ?>"
              >
                My recipes
              </a>
            </li>
            <li>
              <a href="savedrecipes.php?userName=<?=$_SESSION['userName'];?>" class="nav-link bg-warning text-center border-left border-right rounded-0 <?php if ($currentNav == "likerecipes") echo "active"; ?>">
                My favorites
              </a>
            </li>
            <li>
              <a href="profilesetting.php?user=<?=$_SESSION['user_id'];?>" class="nav-link bg-warning text-center border-left border-right rounded-0 <?php if ($currentNav == "profileupdate") echo "active"; ?>">
                Profile setting
              </a>
            </li>
            <li>
              <a href="createRecipe.php" class="nav-link bg-warning text-center border-left border-right rounded-0 <?php if ($currentNav == "add") echo "active"; ?>">
                Create a recipe
              </a>
            </li>
            <li>
              <a href="logout.php" class="nav-link bg-warning text-center border-left border-right rounded-0">    
                Log out
              </a>
            </li>
          </ul>
        </div>

        <div class="container col-md-9 border-right mt-5">
          <!-- <div class="d-flex justify-content-between align-items-center mb-3"> -->
          <div>
            <h4 class="text-right"><?=($pageHead??""); ?></h4>
          </div>