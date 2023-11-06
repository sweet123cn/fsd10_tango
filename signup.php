<?php

require 'dbConnection.php';

$errorMessages="";
$userName="";
$userEmail="";
$pword="";


if($_SERVER['REQUEST_METHOD']=="POST"){

  if(empty($_POST['userName'])) $errorMessages.="user name is required <br>";
  else $userName=$_POST['userName'];
  if(empty($_POST['userEmail'])) $errorMessages.="email is required<br>";
  else $userEmail=$_POST['userEmail'];
  
  if (empty($_POST['userPass'])) {
    $errorMessages .= "Password is required<br>";
} else {
    if (empty($_POST['ConfirmPass'])) {
            $errorMessages .= "Please confirm your password";
        } elseif ($_POST['userPass'] != $_POST['ConfirmPass']) {
            $errorMessages .= "password and confirm password do not match";
        } else {
            
          $pword = $_POST['userPass'];
        }
    } 
    if ($errorMessages == ""){
      $data=[
        "user_name"=>$userName,
    "email"=>$userEmail,
    "upassword"=>password_hash($pword, PASSWORD_DEFAULT),
    "created_date"=>date("Y-m-d")
    ];
    $sql = "INSERT INTO users (user_name, email, user_password, created_datetime) VALUES (:user_name, :email, :upassword,:created_date);";
    $query=$db->prepare($sql);
$query->execute($data);
header("location: login.php");
    }
}


include "includes/header.php";
?>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="row border rounder-5 p-3 bg-white shadow box-area" style="width:930px">
<div class="col-md-6 d-flex justify-content-center align-items-center left-part" style="background:rgba(242, 110, 86, 255)">
 <div class="image">
    <img src="img/signup.webp" class="img-fluid" alt="" style="width:250px;">
  </div>
</div>
<div class="col-md-6 right-part">
 <div class="row align-items-center">
  <div class="mb-4">
  <h3>PANTRYPAGE</h3>
  <p id="slogan">Only delicious food and love cannot be let down</p>
  </div>
  <div class="input-group mb-3">
  <form  action="signup.php" method="post">
        <p><?=$errorMessages; ?></p>
				<div class="modal-header">
            <h3 class="modal-title font-weight-bold">
              Create an account
            </h3>
          </div>
        <div class="form-group mb-3">
            <input class="form-control form-control-lg bg-light fs-6"  name="userName" type="text" required="required" class="form-control" placeholder="Enter your user name">
        </div>
         <div class="form-group mb-1">
            <input class="form-control form-control-lg bg-light fs-6"  name="userEmail" type="email" required="required" class="form-control" placeholder="Enter your Email">
        </div>
        <div class="form-group mb-1">
            <input class="form-control form-control-lg bg-light fs-6" name="userPass" type="password" required="required" class="form-control" placeholder="Enter your Password">
        </div>
        <div class="form-group mb-1">
            <input class="form-control form-control-lg bg-light fs-6"  name="ConfirmPass" type="password" required="required" class="form-control" placeholder="Confirm your Password">
        </div>
        <div class="form-group mb-5">
            <button class="btn btn-lg btn-warning w-100 fs-6" name="btnSubmit" type="submit" >Sign up</button>
        </div>
        <p>
        Already have an account? <a href="login.php">Sign in</a>
        </p>
    </form>
  </div>
  </div>
 </div>
</div>
</div>
<?php include "includes/footer.php"; ?>