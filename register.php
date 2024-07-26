<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './src/PHPMailer.php';
require './src/Exception.php';
require './src/SMTP.php';

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

// Initialize $message variable as an array
$message = array();

if(isset($_POST['submit'])){
    // Step 1: Generate a random OTP
    $otp = mt_rand(100000, 999999); // Generate a 6-digit OTP

    $id = unique_id();
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_files/'.$rename;

    // Step 2: Send the OTP to the user's email
    $to = $email;
    $subject = "OTP for registration";
    $messageBody = "Your OTP for registration is: $otp";

    // Create a new PHPMailer instance
    $mail = new PHPMailer();

    // Enable SMTP debugging (optional)
    $mail->SMTPDebug = 0;

    // Set mailer to use SMTP
    $mail->isSMTP();

    // Specify SMTP server
    $mail->Host = 'smtp.gmail.com';

    // Enable SMTP authentication
    $mail->SMTPAuth = true;

    // SMTP username (your Gmail email address)
    $mail->Username = 'mouryaankit091@gmail.com';

    // SMTP password (your Gmail password)
    $mail->Password = 'lirzrfrdhnslnigb';

    // Enable TLS encryption
    $mail->SMTPSecure = 'tls';

    // TCP port to connect to
    $mail->Port = 587;

    // Set sender email address and name
    $mail->setFrom('mouryaankit091@gmail.com', 'Virtual Zoo');

    // Set recipient email address and name
    $mail->addAddress($to, $name);

    // Set email subject and body
    $mail->Subject = $subject;
    $mail->Body = $messageBody;

    // Send email
    if ($mail->send()) {
        // Store the OTP in a session or in the database for verification later
        $_SESSION['otp'] = $otp;
        // Store user details in session for later retrieval
        $_SESSION['user_details'] = array(
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'password' => $pass,
            'image' => $rename
        );
        // Redirect the user to OTP verification page
        header('location: otp.php');
    } else {
        $message[] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Registration</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3>Create Account</h3>
      <div class="flex">
         <div class="col">
            <p>Your Name <span>*</span></p>
            <input type="text" name="name" placeholder="Enter your name" maxlength="50" required class="box">
            <p>Your Email <span>*</span></p>
            <input type="email" name="email" placeholder="Enter your email" maxlength="100" required class="box">
         </div>
         <div class="col">
            <p>Your Password <span>*</span></p>
            <input type="password" name="pass" placeholder="Enter your password" maxlength="20" required class="box">
            <p>Confirm Password <span>*</span></p>
            <input type="password" name="cpass" placeholder="Confirm your password" maxlength="20" required class="box">
         </div>
      </div>
      <p>Select Picture <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
      <p class="link">Already have an account? <a href="login.php">Login now</a></p>
      <input type="submit" name="submit" value="Register Now" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>

