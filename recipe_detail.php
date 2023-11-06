<?php
// automatically load any library required from composer
require "dbConnection.php"; //connect to db

$defaultImagePath = "https://www.budgetbytes.com/wp-content/uploads/2013/07/How-to-Calculate-Recipe-Costs-H.jpg";

//initialize variables
$recipe_id = "";
$recipe_item = "";
$user_id = "";

// if the GET method does NOT have a key named recipe_id, set the default recipe_id to 1
if (!isset($_GET["recipe_id"])) {
    $detail_logger->warning("There is not recipe_id in GET method!");
    //check whether the recipe_id is set in session variable, if not set the default recipe_id to 1
    if (!isset($_SESSION["recipe_id"])) {
        $detail_logger->warning("There is not recipe_id in {$_SESSION["recipe_id"]}!");
        $recipe_id = 1;
        $detail_logger->info("Set default recipe_id: " . $recipe_id);
    
    } else {
        $recipe_id = $_SESSION["recipe_id"];
    } 
// if the GET method has a key named recipe_id, set the recipe_id to the value 
} else {
    $recipe_id = $_GET["recipe_id"];
}

$detail_logger->info("recipe_id: " . $recipe_id);

//save the recipe_id to session variable
$_SESSION["recipe_id"] = $recipe_id;

//get recipe info from database
$recipe_item = DB::queryFirstRow( "SELECT * FROM fsd10_tango.recipe WHERE recipe_id = %i", $recipe_id );

//check if recipe_id exists in the database
if (empty($recipe_item)) {
    $detail_logger->warning("recipe_id: " . $recipe_id . " not exist in the database!");
}

//get review_id info from database
$review_ids = DB::query( "SELECT review_id FROM fsd10_tango.recipe_review WHERE recipe_id = %i ORDER BY recipe_review_id DESC", $recipe_id );

if (empty($review_ids)) {
    $detail_logger->notice("No review in database for recipe_id: " . $recipe_id);
}

//get user_id from session
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
}

//add review to database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_rating'])) { $add_rating =htmlentities($_POST['add_rating']); }

    if (isset($_POST['add_review'])) { $add_review = htmlentities($_POST['add_review']); }

    //insert data into review table
    if (isset($add_rating) && isset($add_review) && !empty($user_id))
    {
        DB::insert('fsd10_tango.review', array(
                    'rating' => $add_rating,
                    'comment' => $add_review,
                    'user_id' => $user_id
                )); 
        $review_id = DB::insertId(); //get the last insert id
        //insert data into recipe_review table
        DB::insert('fsd10_tango.recipe_review', array(
                    'recipe_id' => $recipe_id,
                    'review_id' => $review_id,
                    'description' => ''
        ));
        //calculate the new rating value
        $review_ids = DB::query("SELECT review_id FROM fsd10_tango.recipe_review WHERE recipe_id = %i",$recipe_id);
        
        $rating_sum = 0;
        foreach ($review_ids as $review_id) {
            $rating = DB::queryFirstRow("SELECT rating FROM fsd10_tango.review WHERE review_id = %i", $review_id["review_id"]);
            $rating_sum = $rating_sum + $rating["rating"];
        }
        $rating_avg = round($rating_sum / count($review_ids));

        //update the rating value in recipe table
        DB::update('fsd10_tango.recipe', array('rating' => $rating_avg), "recipe_id = %i", $recipe_id);
    }

    //check whether the user is logged in
    if (isset($user_id) && !empty($user_id)) {
        //add favorite recipe flag to database
        if (isset($_POST["btnAddFavorite"])) {
            DB::insert('fsd10_tango.favorite_recipe', array(
            'recipe_id' => $recipe_id,
            'user_id' => $user_id
            ));
        }

        //remove favorite recipe flag from database
        if (isset($_POST["btnRemoveFavorite"])) {
            DB::delete('fsd10_tango.favorite_recipe', "recipe_id = %i AND user_id = %i", $recipe_id, $user_id);
        }
    }    
}

include "includes/header.php";

?>


<div class="container mt-5 pt-5 mb-5">
    <div class="mt-3 text-center">
        <?php
            echo '<h2><strong>' . $recipe_item["recipe_name"] . '</strong></h2>';
        ?>
    </div>

    <!-- check if the recipe_image_path is null or empty then set the default image  -->
    <?php if (!isset($recipe_item["recipe_image_path"]) || $recipe_item["recipe_image_path"] == null || strlen($recipe_item["recipe_image_path"]) == 0) {
        $recipe_item["recipe_image_path"] = $defaultImagePath;
    } ?>

    <div class="row justify-content-center mt-3 mb-3">
        <div class="col-12 text-center">
            <div class="card">
                <!-- display recipe image -->
                <img src="<?= $recipe_item["recipe_image_path"] ?>" class="image-fluid" alt="recipe image">
                <!-- display recipe rating -->
                <div class="card-body">
                    <?php for($i=1;$i<=5;$i++) {
                        if($i<=$recipe_item["rating"]) {
                            echo '<i class="bi bi-star-fill checked" style="color: orange"></i>';
                        } else {
                            echo '<i class="bi bi-star "></i>';
                        }
                    };?>
                    <br>
                    <?php
                        $favorite_falg = "";
                        // if user logged in, display favorite recipe icon
                        if (isset($user_id) && !empty($user_id)) {
                            $favorite_falg = DB::queryFirstColumn(
                            "SELECT COUNT(*) as count FROM fsd10_tango.favorite_recipe WHERE user_id = %i AND recipe_id = %i",
                            $user_id,
                            $recipe_id
                        );
                        if ($favorite_falg[0] > 0)
                            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill fav" viewBox="0 0 16 16" style="color: rgb(214, 47, 10);">
                            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                            </svg>';
                        else
                            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16" style="stroke: rgb(214, 47, 10);">
                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                            </svg>';
                        } else {
                            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16" style="stroke: rgb(214, 47, 10);">
                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                            </svg>';
                        }
                    ?>
                    <br>
                    <?php
                        //check whether the user is logged in, if yes, enable add favorite recipe button and remove favorite recipe button
                        if (isset($user_id) && !empty($user_id)) {
                            if ($favorite_falg[0] > 0) {
                    ?>
                    <form class="add-favorite-form" id="add_favorite_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <button id="btnRemoveFavorite" name="btnRemoveFavorite" class="btn btn-primary">Remove favorite</button>
                    </form>
                    <?php    
                            } else {
                    ?>
                    <form class="add-favorite-form" id="add_favorite_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <button id="btnAddFavorite" name="btnAddFavorite" class="btn btn-primary">Add favorite</button>
                    </form>
                    <?php
                            } 
                        // if user not logged in, disable add favorite recipe button   
                        } else {
                    ?>
                    <form class="add-favorite-form" id="add_favorite_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <button id="btnAddFavorite" name="btnAddFavorite" class="btn btn-primary" disabled>Add favorite</button>
                    </form>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- display recipe description -->
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h4>DESCRIPTION</h4>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stopwatch" viewBox="0 0 16 16">
                    <path d="M8.5 5.6a.5.5 0 1 0-1 0v2.9h-3a.5.5 0 0 0 0 1H8a.5.5 0 0 0 .5-.5V5.6z"/>
                    <path d="M6.5 1A.5.5 0 0 1 7 .5h2a.5.5 0 0 1 0 1v.57c1.36.196 2.594.78 3.584 1.64a.715.715 0 0 1 .012-.013l.354-.354-.354-.353a.5.5 0 0 1 .707-.708l1.414 1.415a.5.5 0 1 1-.707.707l-.353-.354-.354.354a.512.512 0 0 1-.013.012A7 7 0 1 1 7 2.071V1.5a.5.5 0 0 1-.5-.5zM8 3a6 6 0 1 0 .001 12A6 6 0 0 0 8 3z"/>
                </svg>
                <?= $recipe_item["cooking_time"] ?>mins  
        </div>
        <div class="card-body">
            <p class="card-text">
                <?= nl2br($recipe_item["description"]) ?>
            </p>
        </div>
    </div>

    <!-- display recipe material -->
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h4>MATERIAL</h4>
        </div>
        <div class="card-body">
            <p class="card-text">
                <?= nl2br($recipe_item["material_description"]) ?>
            </p>
        </div>
    </div>

    <!-- display recipe instruction -->
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h4>INSTRUCTION</h4>
        </div>
        <div class="card-body">
            <p class="card-text">
                <?= nl2br($recipe_item["step_instruction"]) ?>
            </p>
        </div>
    </div>

    <!-- let user leave recipe review -->
    <h4>Reviews</h4>
    <form id="add_review_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="mb-3">
            <label for="add_rating" class="form-label">How would you rate this(1-5)?</label>
            <input type="number" name="add_rating" id="add_rating" value="5" required min="1" max="5">
        </div>
        <div class="form-floating mb-3">
            <textarea id="add_review" name="add_review" class="form-control" placeholder="Leave a comment here" style="height: 100px" required></textarea>
            <label for="add_review">Comments</label>
        </div>
        <?php 
            // if user is logged in, enable submit button
            if (isset($user_id) && !empty($user_id)) { 
        ?>
        <button type="submit" class="btn btn-primary" id="btnLeaveReview" name="btnLeaveReview">Submit</button>
        <?php 
            // if user is not logged in, disable submit button
            } else { 
        ?>
        <button type="submit" class="btn btn-primary" id="btnLeaveReview" name="btnLeaveReview" disabled>Submit</button>
        <?php } ?>
    
    </form>

    <div class="reviews-display mt-3" style="border-top: 3px solid rgba(252, 171, 23, 255);">

    <!-- retrieve review information from database -->
    <?php foreach ($review_ids as $review_id) {
        //get review information
        $review_item = DB::queryFirstRow("SELECT * FROM fsd10_tango.review WHERE review_id = %i", $review_id["review_id"]);
        //get user information
        $user_item = DB::queryFirstRow("SELECT * FROM fsd10_tango.users WHERE user_id = %i", $review_item["user_id"]);
    ?>
        <div class="card mt-2 mb-2">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4><?= $user_item["user_name"] ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php for($i=1;$i<=5;$i++) {
                            if($i<=$review_item["rating"]) {
                                echo '<i class="bi bi-star-fill checked" style="color: orange"></i>';
                            } else {
                                echo '<i class="bi bi-star "></i>';
                            }
                        };?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <p class="h6"><?= $review_item["comment"] ?></p>
                    <footer class="blockquote-footer text-end">Created: <?= $review_item["created_datetime"]; ?></footer>
                </blockquote>
            </div>
        </div>
    <?php }; ?>
</div>
<?php include "includes/footer.php"; ?>