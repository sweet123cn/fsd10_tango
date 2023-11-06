<?php

session_start(); // allows the use of $_SESSION



$userIsLoggedIn = $_SESSION['loggedIn'] ?? false;

require "includes/projectfunctions.php";


$dbType = "mysql"; 
$dbServer = "fsd10-demo-mysql-admin.mysql.database.azure.com"; 
$dbName = "fsd10_tango"; 
$dbPort = "3306"; 
$dbCharset = "utf8"; 
$dbUsername = "fsduser"; 
$dbPassword = "myDBpw"; 


$dbDSN = "{$dbType}:host={$dbServer};dbname={$dbName};port={$dbPort};charset={$dbCharset}";


$db = new PDO($dbDSN, $dbUsername, $dbPassword);


$sql = "SELECT meal_id, meal_name FROM meal ";
$query = $db->query($sql);
$allMeals = $query->fetchAll(PDO::FETCH_KEY_PAIR);

$sql = "SELECT cuisine_id, cuisine_type FROM cuisine ";
$query = $db->query($sql);
$allCuisines = $query->fetchAll(PDO::FETCH_KEY_PAIR);


$sql = "SELECT ingredient_id, ingredient_name FROM ingredients ";
$query = $db->query($sql);
$allIngredients = $query->fetchAll(PDO::FETCH_KEY_PAIR);

require 'vendor/autoload.php';




//use meekro library to connect to mysql db
DB::$host = "fsd10-demo-mysql-admin.mysql.database.azure.com";
DB::$user = "fsduser";
DB::$password = "myDBpw";
DB::$dbName = "fsd10_tango";
DB::$port = "3306";
 
 
 
// add monolog library to project
use Monolog\Logger; //
use Monolog\Handler\StreamHandler; //controll saving to a file
 
//set local timezone for logging
date_default_timezone_set("America/Montreal");
 
//create a seperate logger for different purposes
$home_logger = new Logger('home_logger');
$detail_logger = new Logger('detail_logger');
//where/how to save the log data
$home_logger->pushHandler( new StreamHandler('./logs/home.log') );
$detail_logger->pushHandler( new StreamHandler('./logs/detail.log') );
 
//test add logger to dbs
$home_logger->info("this is a test message: logger from dbConnection.php");
$detail_logger->info("this is a test message: logger from dbConnection.php");
 



?>