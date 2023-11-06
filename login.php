<?php

require 'dbConnection.php';


$errorMessages = "";


if ($_SERVER['REQUEST_METHOD'] == "POST"){

	
	if (validateIsEmptyData($_POST, 'txtLogin')) $errorMessages .= "Username is required <br>";
	
	if (validateIsEmptyData($_POST, 'txtPass')) $errorMessages .= "Password is required <br>";
	
	
	if ($errorMessages == ""){
	
		$sql = "SELECT * FROM users WHERE user_name = :user";
		$query = $db->prepare($sql);
		$query->execute(["user"=> $_POST['txtLogin']]);
		$data = $query->fetch();
		
		if ($data){
		
			if (password_verify($_POST['txtPass'],$data['user_password'])){
				// The username and password match

				// keep the user logged using session variables 
				$_SESSION['loggedIn'] = true;
				$_SESSION['userName'] = $data['user_name'];
				$_SESSION['user_id'] = $data['user_id'];
				$_SESSION['user_email'] = $data['email'];
				$_SESSION['user_image'] = $data['profile_image_path'];


				// redirect
				header("location: index.php");
		
			}
		}
		// username does not exits in the database OR password does not match
		$errorMessages = "Invalid Credentials";
		
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
   <form  action="login.php" method="post">
        <p><?=$errorMessages; ?></p>
				<div class="modal-header">
            <h3 class="modal-title font-weight-bold" id="loginModalLabel">
              Login
            </h3>
          </div>
					<div>New here? <a href="signup.php">Create Account</a></div>
        <div class="form-group mb-3">
            <label for="txtLogin" class="form-label">Username</label>
            <input id="txtLogin" name="txtLogin" class="form-control form-control-lg bg-light fs-6" type="text" required="required" class="form-control">
        </div>
        <div class="form-group mb-3">
            <label for="txtPass" class="form-label">Password</label>
            <input id="txtPass" name="txtPass" class="form-control form-control-lg bg-light fs-6" type="password" required="required" class="form-control">
        </div>
        <div class="form-group mb-3">
            <button name="btnSubmit" type="submit" class="btn btn-lg btn-warning w-100 fs-6">Login</button>
        </div>
    </form>
  </div>
  </div>
 </div>
</div>
</div>

<?php include "includes/footer.php"; ?>