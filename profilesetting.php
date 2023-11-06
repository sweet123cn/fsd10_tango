<?php
require "dbConnection.php";


$errorMessages="";
$userName="";
$phoneNumber="";
$country="";
$province="";
$email="";
$pword="";
// echo "<br>";
//   echo "12345";
//   print_r($_GET);
if (array_key_exists('user', $_GET)){
    $query=$db->prepare("SELECT * FROM users WHERE user_id = :id");
    $query->execute(['id'=>$_GET['user']]);
    $data=$query->fetch();
    if(!$data){
        pageNotFound();
    }
    $userName=$data['user_name'];
    $phoneNumber=$data['phonenumber'];
    $country=$data['country'];
    $province=$data['province'];
    $email=$data['email'];
    $fileImage=$data['profile_image_path'];
    $userID=$data['user_id'];


  // echo "<br>";
  // echo "12345";
  // echo "<pre>";
  //   print_r($data);
  //   echo "</pre>";
    $pword=$data['user_password'];
}
if($_SERVER['REQUEST_METHOD']=="POST"){
    if(validateIsEmptyData($_POST,'userName')) $errorMessages.="user name is required <br>";
    else $userName=$_POST['userName'];
    if(validateIsEmptyData($_POST,'phoneNumber')) $errorMessages.="phone number is required<br>";
    else $phoneNumber=$_POST['phoneNumber'];
    if(validateIsEmptyData($_POST,'country')) $errorMessages.="country is required<br>";
    else $country=$_POST['country'];
    if(validateIsEmptyData($_POST,'province')) $errorMessages.="province is required<br>";
    else $province=$_POST['province'];
    if(validateIsEmptyData($_POST,'email')) $errorMessages.="email is required<br>";
    else $email=$_POST['email'];

    if (empty($_POST['password'])) {
      $errorMessages .= "Password is required<br>";
  } else {
      if (password_verify($_POST['password'],$pword)) {
          if (empty($_POST['newPassword'])) {
            $pword=$_POST['password'];
          } elseif (empty($_POST['confirmPassword'])) {
              $errorMessages .= "Please confirm your new password";
          } elseif ($_POST['newPassword'] != $_POST['confirmPassword']) {
              $errorMessages .= "New password and confirm password do not match";
          } else {
              
            $pword = $_POST['newPassword'];
          }
      } else {
          $errorMessages .= "Password is not correct";
      }
  }
             
             $fileImage=$_POST['oldImage'];
             $userID=$_POST['userID'];
        
             if ($errorMessages == ""){
                if ($_FILES['fileImage']['error'] == 0){
                    $sourceFile = $_FILES['fileImage']['tmp_name'];
                    $destinationFile = "upload/" . $_FILES['fileImage']['name'];
        
                    if (move_uploaded_file($sourceFile, $destinationFile)){
                     
                        if ($fileImage  != "" && $fileImage != $destinationFile){
                            unlink($fileImage); 
                        }
                        $fileImage = $destinationFile;
                    } else {
                        // file has NOT been moved
                    }
            
                } 

$data=[
    "userName"=>$userName,
"phonenumber"=>$phoneNumber,
"country"=>$country,
"province"=>$province,
"email"=>$email,
"upassword"=>password_hash($pword, PASSWORD_DEFAULT),
"created_date"=>date("Y-m-d"),
"profile_image_path"=>$fileImage,
"userid"=>$userID
];
// echo "<pre>";
//     print_r($data);
//     echo "</pre>";
$sql="UPDATE users SET user_name = :userName, phonenumber = :phonenumber, country = :country, province = :province, email = :email, user_password = :upassword, created_datetime = :created_date, profile_image_path = :profile_image_path WHERE user_id= :userid";
$query=$db->prepare($sql);
$query->execute($data);
header("location: login.php");

}
}
$pageHead="Profile Settings";
$currentNav="profileupdate";
include "sidebar.php";
?>
 
            <form class="p-3 py-5" method="POST" enctype="multipart/form-data" action="profilesetting.php?user=<?=$_SESSION['user_id'];?>">
            <input type="hidden" name="userID" value="<?=$userID; ?>" >
		<input type="hidden" name="oldImage" value="<?= $fileImage; ?>" ?>
           <p><?=$errorMessages ?></p>
            <div class="row mt-2">
              <div class="col-md-6 form-group">
                <label for="userName" class="lables">userName</label>
               <input id=userName name="userName" required="required"
                  type="text"
                  class="form-control"
                  placeholder="Enter username"
                  value="<?=$userName;?>"
                />
              </div>
              <div class="col-md-6 form-group">
                <label for="phoneNumber" class="labels">Phone Number</label
                ><input id="phoneNumber" name="phoneNumber"
                  type="text"
                  class="form-control"
                  value="<?=$phoneNumber;?>"
                  placeholder="phone number"
                />
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6 form-group">
                <label for="country" class="labels">Country</label
                ><input id="country" name="country"
                  type="text"
                  class="form-control"
                  placeholder="country"
                  value="<?=$country;?>"
                />
              </div>
              <div class="col-md-6 form-group">
                <label for="province" class="labels">Province/Region</label
                ><input id="province" name="province"
                  type="text"
                  class="form-control"
                  value="<?=$province;?>"
                  placeholder="province"
                />
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6 form-group">
                <label for="email" class="labels">Email</label>
<input id="email" name="email"
                  type="email"
                  class="form-control"
                  placeholder="enter your email"
                  value="<?=$email;?>"
                />
              </div>
              <div class="col-md-6 form-group">
                <label for="password" class="labels">Password</label
                ><input id="password" name="password"
                  type="password"
                  class="form-control"
                  placeholder="enter your password"
                  value=""
                />
              </div>
              
              <div class="row mt-3">
                <div class="col-md-6 form-group">
                <label for="newPassword" class="labels">New password</label
                ><input id="newPassword" name="newPassword"
                  type="password"
                  class="form-control"
                  placeholder="enter new password"
                  value=""
                />
              </div>
              <div class="col-md-6 form-group">
                <label for="confirmPassword" class="labels">Confirm Password</label
                ><input id="confirmPassword" name="confirmPassword"
                  type="password"
                  class="form-control"
                  placeholder="enter again new password"
                  value=""
                />
              </div>
            </div>
            <div class="col-md-12 mt-3 form-group">
              <label for="fileImage" class="labels">Update your image</label
              ><input id="fileImage" name="fileImage"
                type="file"
                class="form-control"
              />
            </div>
            <div class="mt-5 text-center form-group">
              <button name="btnSubmit" class="btn btn-warning" type="submit">
                Save Profile
              </button>
            </div>
            </form>
            <?php include "includes/footer.php"; ?>  