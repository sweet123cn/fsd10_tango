<?php
require "dbConnection.php";
$errorMessages = "";
$uName="";
$uEmail="";
$uMessage="";

if($_SERVER['REQUEST_METHOD']=="POST"){
  if (empty($_POST['uName'])) $errorMessages .= "name is required <br>";
	else $uName = $_POST['uName'];

if (empty($_POST['email'] )) $errorMessages .= "email is required <br>";
else if((!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))
  $errorMessages .= 'Email is invalid';
	else $uEmail = $_POST['email'];

if (empty($_POST['message'])) $errorMessages .= "message is required <br>";
	else $uMessage = $_POST['message'];

if($errorMessages==""){
  $data = [
    "m_name" => $uName, 
    "m_email" => $uEmail,
    "m_message" => $uMessage,
    "m_time" => date("Y-m-d")
  ];
  $sql = "INSERT INTO messages (m_name, m_email, m_message, m_datetime) VALUES (:m_name, :m_email, :m_message,:m_time);";
  $query = $db->prepare($sql);			
		$query->execute($data);

    $errorMessages .= "Thank you! your message sent succesfully!<br>";
  // $mail = new PHPMailer();


  // // specify SMTP credentials


  // $mail->isSMTP();
  // $mail->Host = 'smtp.mailtrap.io';
  // $mail->SMTPAuth = true;
  // $mail->Username = 'your_smtp_username';
  // $mail->Password = 'your_smtp_password';
  // $mail->SMTPSecure = 'tls';
  // $mail->Port = 2525;
  // $mail->setFrom($email, 'Mailtrap Website');
  // $mail->addAddress('example@example.com', 'Me');
  // $mail->Subject = 'New message from your website';

 
  // $mail->isHTML(true);
  // $bodyParagraphs = ["Name: {$name}", "Email: {$email}", "Message:", nl2br($message)];
  // $body = join('<br />', $bodyParagraphs);
  // $mail->Body = $body;
  // echo $body;

  // if($mail->send()){
  //     header('Location: thank-you.html'); // 
  // } else {

  //     $errorMessage = 'Oops, something went wrong. Mailer Error: ' . $mail->ErrorInfo;
  // }

  // $toEmail="";
  // $emailSubject = "New message from your contact form";
  // $headers= ['From'=>$uEmail, 'Reply-To'=>$uEmail,'Content-type'=>"text/html; charset=utf-8"];
  // $bodyParagraphs = ["Name: {$uName}", "Email: {$uEmail}", "Message:", $uMessage];
  // $body = join(PHP_EOL, $bodyParagraphs);

  // if(mail($toEmail, $emailSubject,$body, $headers)){
  //   $errorMessages.="Thank you! your message sent succesfully!";
  // }else{
  //    $errorMessages.="Oops, something went wrong. Please try again.";
  // }
 
}
}

include "includes/header.php";
?>
<div class="container mt-5">
      <section id="about" class="about sectionpadding">
        <div class="container">
          <div class="row">
            <div class="col-lg-5 col-md-12 col-12">
              <div class="about-img">
                <img src="img/about.jpg" alt="" class="img-fluid" />
              </div>
            </div>
            <div class="col-lg-7 col-md-12 col-12 ps-lg-5 mt-md-5">
              <div class="about-text">
                <h2>Our Story</h2>
                <h4 id="slogan">Only delicious food and love cannot be let down</h4>
                <p>
                Founded in 2023 in Montreal, PANTRYPAGE changed the food world by sharing recipes and cooking tips, while celebrating the expertise of home cooks online. Since then, PANTRYPAGE has become the world's famous community-driven food brand, providing trusted resources to more than 60 million home cooks each month.
                </p>
                <a href="#" class="btn btn-warning">Learn More</a>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section id="team" class="team sectionpadding">
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <div class="section-header text-center ">
                <h2>Our Team</h2>
                <p>
                  PANTRYPAGE is and always has been a community built around love.<br> We are people who love food, love to cook, and love to share recipes and stories...
                </p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-6 col-lg-4">
              <div class="card text-center">
                <div class="card-body">
                  <img
                    src="img/download.png"
                    alt=""
                    class="img-fluid rounded-circle"
                  />
                  <h4 class="card-title py-2">Shixin Tang</h4>
                  <p class="card-text">      
                    Co-Founder.Before falling in love with food media, Shixin has more than 10 year experience in field of computer science.
                  </p>

                  <p class="socials">
                    <i class="bi bi-twitter text-dark mx-1"></i>
                    <i class="bi bi-facebook text-dark mx-1"></i>
                    <i class="bi bi-linkedin text-dark mx-1"></i>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
              <div class="card text-center">
                <div class="card-body">
                  <img
                    src="img/download.png"
                    alt=""
                    class="img-fluid rounded-circle"
                  />
                  <h4 class="card-title py-2">Xing Huang</h4>
                  <p class="card-text">
                  Co-founder.Xing is passionate about the food and cooking stories that center people, place, and culture. She works also as recipe developer and social media editor.
                  </p>

                  <p class="socials">
                    <i class="bi bi-twitter text-dark mx-1"></i>
                    <i class="bi bi-facebook text-dark mx-1"></i>
                    <i class="bi bi-linkedin text-dark mx-1"></i>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
              <div class="card text-center">
                <div class="card-body">
                  <img
                    src="img/download.png"
                    alt=""
                    class="img-fluid rounded-circle"
                  />
                  <h4 class="card-title py-2">Lisi Cao</h4>
                  <p class="card-text">
                    Co-founder.Lisi has an entensive food and editorial background.He is an avid home cook and lover of all things food related.
                  </p>

                  <p class="socials">
                    <i class="bi bi-twitter text-dark mx-1"></i>
                    <i class="bi bi-facebook text-dark mx-1"></i>
                    <i class="bi bi-linkedin text-dark mx-1"></i>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section id="contact" class="contact sectionpadding">
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <div class="section-header text-center ">
                <h2>Contact Us</h2>
                <p>
                  Welcome to PATRPAGE! <br>We are here to answer all your questions.
                </p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 pt-2">
              <h4>Contact Info</h4>
              <div>
                <i class="fa fa-map-marker"></i>
                <p>4517 Anywhere St. Montreal, QC A1B 2C3</p>
              </div>
              <div>
                <i class="fa fa-phone"></i>
                <p>514.123.4567</p>
              </div>
              <div>
                <i class="fa fa-envelope"></i>
                <p><a href="mailto:tango@email.com">tango@email.com</a></p>
              </div>
            </div>
            <div class="col-md-6 p-0 pb-2">
              <form action="aboutus.php" method="POST" class="bg-light p-4 m-auto">
                <h4>Send a message</h4>
                <?php if($errorMessages!=""){?>
                 <p><?=$errorMessages ?></p>
                <?php } ;?>
                <div class="row">
                  <div class="col-md-6 form-group">
                    <div class="mb-3 border">
                      <input
                        type="text"
                        name="uName"
                        class="form-control"
                        required
                        placeholder="your full name"
                      />
                    </div>
                  </div>
                  <div class="col-md-6 form-group">
                    <div class="mb-3 border">
                      <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                        placeholder="your Email"
                      />
                    </div>
                  </div>
                </div>
                <div class="col-md-12 form-group">
                  <div class="mb-3 border">
                    <textarea
                    name="message"
                      rows="3"
                      required
                      class="form-control"
                      placeholder="Type massage here"
                    ></textarea>
                  </div>
                </div>
                <button name="btnSubmit" type="submit" class="btn btn-warning btn-lg btn-block mt-3">
                  Send Now
                </button>
              </form>
            </div>
          </div>
        </div>
      </section>
    </div>
<?php
include "includes/footer.php";
?>