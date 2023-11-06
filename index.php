<?php

// automatically load any library required from composer
require "dbConnection.php"; //connect to db

//initialize the user_id
$user_id = "";

//get the logged in user_id
if (isset($_SESSION["user_id"])) {
    $home_logger->notice("SESSION user_id is already set, user_id = {$_SESSION["user_id"]}");
    $user_id = $_SESSION["user_id"];
}
else {
    $home_logger->notice("SESSION user_id is not set");
}

//check if the filter session variable is set,if not,initialize the session
if (!isset($_SESSION["home_filter_values"])) {
    $_SESSION["home_filter_values"]["meal_selected"] = [
        "id" => "",
        "name" => "Meal",
    ];
    $_SESSION["home_filter_values"]["ingredient_selected"] = [
        "id" => "",
        "name" => "Ingredient",
    ];
    $_SESSION["home_filter_values"]["cuisine_selected"] = [
        "id" => "",
        "name" => "Cuisine",
    ];
}

//check if the search string session variable is set,if not,initialize the session
if (!isset($_SESSION["search_string"])) {
    $_SESSION["search_string"] = "Search for a recipe?";
}

//default image path
$defaultImagePath ="https://www.budgetbytes.com/wp-content/uploads/2013/07/How-to-Calculate-Recipe-Costs-H.jpg";

//set default value for meal, cuisine and ingredient
$meal_selected = "";
$ingredient_selected = "";
$cuisine_selected = "";
$meal_name = "";
$ingredient_name = "";
$cuisine_type = "";

//check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["btnFilter"])) {
        //save the selected meal, ingredient and cuisine values to the session
        //according to the selected meal, ingredient and cuisine to get the selected name or description
        if (isset($_POST["meal_selected"])) {
            $meal_selected = $_POST["meal_selected"];
            $meal_name = DB::queryFirstRow("SELECT meal_name FROM fsd10_tango.meal where meal_id = %i", $meal_selected);
            $_SESSION["home_filter_values"]["meal_selected"] = [
                "id" => $meal_selected,
                "name" => $meal_name["meal_name"],
            ];
        }

        if (isset($_POST["ingredient_selected"])) {
            $ingredient_selected = $_POST["ingredient_selected"];
            $ingredient_name = DB::queryFirstRow("SELECT ingredient_name FROM fsd10_tango.ingredients where ingredient_id = %i", $ingredient_selected);
            $_SESSION["home_filter_values"]["ingredient_selected"] = [
                "id" => $ingredient_selected,
                "name" => $ingredient_name["ingredient_name"],
            ];
        }

        if (isset($_POST["cuisine_selected"])) {
            $cuisine_selected = $_POST["cuisine_selected"];
            $cuisine_type = DB::queryFirstRow("SELECT cuisine_type FROM fsd10_tango.cuisine where cuisine_id = %i", $cuisine_selected);
            $_SESSION["home_filter_values"]["cuisine_selected"] = [
                "id" => $cuisine_selected,
                "name" => $cuisine_type["cuisine_type"],
            ];
        }
    //if reset button is clicked, clear the session variable
    } elseif (isset($_POST["btnReset"])) {
        $meal_selected = "";
        $ingredient_selected = "";
        $cuisine_selected = "";
        $meal_name = "";
        $ingredient_name = "";
        $cuisine_type = "";
        $_SESSION["home_filter_values"]["meal_selected"] = [
            "id" => "",
            "name" => "Meal",
        ];
        $_SESSION["home_filter_values"]["ingredient_selected"] = [
            "id" => "",
            "name" => "Ingredient",
        ];
        $_SESSION["home_filter_values"]["cuisine_selected"] = [
            "id" => "",
            "name" => "Cuisine",
        ];
        $_SESSION["search_string"] = "";
    }
}

//get meal list from meal table
$meal_items = DB::query("SELECT * FROM fsd10_tango.meal");

//get ingredients list from ingredients table
$ingredients_items = DB::query("SELECT * FROM fsd10_tango.ingredients");

//get cuisine list from cuisine table
$cuisine_items = DB::query("SELECT * FROM fsd10_tango.cuisine");

//according with the selected filter options and search string, retrive recipes data from recipe table
//prepare the select conditions
$conditions = [];

if (!empty($_SESSION["home_filter_values"]["meal_selected"]["id"])) {
    $conditions[] =
        "meal_id = " . $_SESSION["home_filter_values"]["meal_selected"]["id"];
}

if (!empty($_SESSION["home_filter_values"]["cuisine_selected"]["id"])) {
    $conditions[] =
        "cuisine_id = " .
        $_SESSION["home_filter_values"]["cuisine_selected"]["id"];
}

if (!empty($_SESSION["home_filter_values"]["ingredient_selected"]["id"])) {
    $conditions[] =
        "recipe_id IN (SELECT recipe_id FROM fsd10_tango.recipe_ingredient WHERE ingredient_id = " .
        $_SESSION["home_filter_values"]["ingredient_selected"]["id"] .
        " )";
}

if (!empty($_SESSION["search_string"]) && $_SESSION["search_string"] != "Search for a recipe?") {
    $conditions[] =
        "recipe_id IN (SELECT DISTINCT recipe_id FROM fsd10_tango.recipe WHERE recipe_name LIKE '%" .
        $_SESSION["search_string"] .
        "%' OR description LIKE '%" .
        $_SESSION["search_string"] .
        "%' )";
}

//prepare the query
if (!empty($conditions)) {
    $whereClause = "WHERE " . implode(" AND ", $conditions);
    
    $query = "SELECT * FROM fsd10_tango.recipe $whereClause";

    //execute the query
    $recipe_items = DB::query($query);

    // if no recipe meet the query conditions, retrive all recipes
    if (empty($recipe_items)) {
        $recipe_items = DB::query("SELECT * FROM fsd10_tango.recipe");
        $_SESSION["home_filter_values"]["meal_selected"] = [
            "id" => "",
            "name" => "Meal",
        ];
        $_SESSION["home_filter_values"]["ingredient_selected"] = [
            "id" => "",
            "name" => "Ingredient",
        ];
        $_SESSION["home_filter_values"]["cuisine_selected"] = [
            "id" => "",
            "name" => "Cuisine",
        ];
        $_SESSION["search_string"] = "";
    }
} else {
    // // if query condition is empty, retrive all recipes
    $recipe_items = DB::query("SELECT * FROM fsd10_tango.recipe");
        $_SESSION["home_filter_values"]["meal_selected"] = [
            "id" => "",
            "name" => "Meal",
        ];
        $_SESSION["home_filter_values"]["ingredient_selected"] = [
            "id" => "",
            "name" => "Ingredient",
        ];
        $_SESSION["home_filter_values"]["cuisine_selected"] = [
            "id" => "",
            "name" => "Cuisine",
        ];
        $_SESSION["search_string"] = "";
}
include "includes/header.php";
$currentNav == "home"
?>

    <div class="container mb-3 home" style="margin-top: 80px;">
        <form class="col-md-12 filters mt-5" id="recipe_filter_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="row">
                <!-- Meal dropdown list -->
                <div class="col-md-3 form-group">
                    <select class="form-select bg-warning text-white" name="meal_selected" id="meal_selected">
                        <option value="meal" id="meal" disabled selected>
                            <?= $_SESSION["home_filter_values"]["meal_selected"]["name"] ?>
                        </option>
                        <?php foreach ($meal_items as $item): ?>
                            <option value="<?= $item["meal_id"] ?>"><?= $item["meal_name"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ingredient dropdown list -->
                <div class="col-md-3 form-group">
                    <select class="form-select bg-warning text-white" name="ingredient_selected" id="ingredient_selected">
                        <option value="ingredient" id="ingredient" disabled selected>
                            <?= $_SESSION["home_filter_values"]["ingredient_selected"]["name"] ?>
                        </option>
                        <?php foreach ($ingredients_items as $item): ?>
                            <option value="<?= $item["ingredient_id"] ?>"><?= $item["ingredient_name"] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Cuisine dropdown list -->
                <div class="col-md-3 form-group">
                    <select class="form-select bg-warning text-white" name="cuisine_selected" id="cuisine_selected">
                        <option value="cuisine" id="cuisine" disabled selected>
                            <?= $_SESSION["home_filter_values"]["cuisine_selected"]["name"] ?>
                        </option>
                        <?php foreach ($cuisine_items as $item): ?>
                            <option value="<?= $item["cuisine_id"] ?>"><?= $item["cuisine_type"] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- add filter button -->
                <div class="col-md-2 form-group">
                    <button id="btnFilter" name="btnFilter" class="btn btn-warning">Filter</button>
                </div>

                <!-- add reset button -->
                <div class="col-md-1 form-group">
                    <button id="btnReset" name="btnReset" class="btn btn-warning">Reset</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Display recipes list in BootStrap cards -->
    <div class="container mb-3">
        <div class="row">
            <?php foreach ($recipe_items as $item){ ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 text-center">
                        <div class="card-body d-flex flex-column align-items-center">
                            <!-- if the recipe_image_path is null or empty then set the default image -->
                            <?php if (!isset($item["recipe_image_path"]) || $item["recipe_image_path"] == null ||strlen($item["recipe_image_path"]) == 0) {
                                $item["recipe_image_path"] = $defaultImagePath;
                            } ?>
                            <a href="recipe_detail.php?recipe_id=<?= $item["recipe_id"] ?>" style="text-decoration: none;">
                                <img src="<?= $item["recipe_image_path"] ?>" alt="recipe image" class="img-fluid" style="height: 250px;"/>
                            </a>

                            <!-- display recipe name -->
                            <h6 class="card-title py-2"><a href="recipe_detail.php?recipe_id=<?= $item["recipe_id"] ?>"><?= $item["recipe_name"] ?></a></h6>
                            <!-- display cooking time -->
                            <p class="card-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-stopwatch" viewBox="0 0 16 16">
                                    <path d="M8.5 5.6a.5.5 0 1 0-1 0v2.9h-3a.5.5 0 0 0 0 1H8a.5.5 0 0 0 .5-.5V5.6z"/>
                                    <path d="M6.5 1A.5.5 0 0 1 7 .5h2a.5.5 0 0 1 0 1v.57c1.36.196 2.594.78 3.584 1.64a.715.715 0 0 1 .012-.013l.354-.354-.354-.353a.5.5 0 0 1 .707-.708l1.414 1.415a.5.5 0 1 1-.707.707l-.353-.354-.354.354a.512.512 0 0 1-.013.012A7 7 0 1 1 7 2.071V1.5a.5.5 0 0 1-.5-.5zM8 3a6 6 0 1 0 .001 12A6 6 0 0 0 8 3z"/>
                                </svg>
                                <?= $item["cooking_time"] ?>mins
                            </p>
                        </div>

                        <!-- display rating -->
                        <div class="card-footer">
                            <?php for($i=1;$i<=5;$i++) {
                                if($i<=$item["rating"]) {
                                    echo '<i class="bi bi-star-fill checked" style="color: orange"></i>';
                                } else {
                                    echo '<i class="bi bi-star "></i>';
                                }
                            };?>

                            <br>

                            <!-- display favorite icon -->
                            <?php
                                $favorite_falg = "";
                                //check whether the user is logged in
                                if (isset($user_id)) {
                                    $favorite_falg = DB::queryFirstColumn("SELECT COUNT(*) as count FROM fsd10_tango.favorite_recipe WHERE user_id = %i AND recipe_id = %i", $user_id, $item["recipe_id"]);
                        
                                    if ($favorite_falg[0] > 0) {
                                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill fav" viewBox="0 0 16 16" style="color: rgb(214, 47, 10);">
                                        <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
                                        </svg>';
                                    }
                                    else {
                                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16" style="stroke: rgb(214, 47, 10);">
                                        <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                        </svg>';
                                    }

                                } else {
                                    echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16" style="stroke: rgb(214, 47, 10);">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                    </svg>';
                                }
                            ?>

                            <br>

                            <?php //calculate the numbers of reviews
                                $review_count = DB::queryFirstColumn("SELECT COUNT(review_id) FROM fsd10_tango.recipe_review WHERE recipe_id = %i", $item["recipe_id"]); 
                            ?>

                            <div class="review-count">Reviews: <?= $review_count[0] ?></div>
                        </div>
                    </div>
                </div>
            <?php }; ?>
        </div>
    </div>



<?php include "includes/footer.php"; ?>