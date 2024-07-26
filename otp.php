<?php
session_start();

// Include database connection
include 'components/connect.php';

// Check if OTP is submitted
if(isset($_POST['submit'])){
    // Retrieve the entered OTP
    $entered_otp = $_POST['otp'];

    // Retrieve the stored OTP from the session
    if(isset($_SESSION['otp'])) {
        $stored_otp = $_SESSION['otp']; // Assuming OTP is stored in session

        // Compare the entered OTP with the stored OTP
        if($entered_otp == $stored_otp){
            // OTP is valid
            if(isset($_SESSION['user_details'])) {
                // Insert user details into the database
                $user_details = $_SESSION['user_details'];
                $id = $user_details['id'];
                $name = $user_details['name'];
                $email = $user_details['email'];
                $password = $user_details['password'];
                $image = $user_details['image'];
                
                $insert_user = $conn->prepare("INSERT INTO `users` (id, name, email, password, image) VALUES (?, ?, ?, ?, ?)");
                $insert_user->execute([$id, $name, $email, $password, $image]);
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploaded_files/'.$image);

                // Clear the OTP and user details from session after successful verification
                unset($_SESSION['otp']);
                unset($_SESSION['user_details']);

                // Set the user ID in the session
                $_SESSION['user_id'] = $id;

                // Redirect to home.php
                header("location: home.php?id=$id");
                exit();
            } else {
                $error_message = "User details not found in session.";
            }
        } else {
            // Invalid OTP
            $error_message = "Invalid OTP. Please try again.";
        }
    } else {
        // OTP session not found
        $error_message = "OTP session not found. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>OTP Verification</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<section class="form-container">
    <h2>Verify OTP</h2>
    <?php if(isset($error_message) && !empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php else: ?>
    <form method="post" class="login">
        <label for="otp">Enter OTP:</label><br>
        <input type="text" id="otp" name="otp" required><br><br>
        <input type="submit" name="submit" value="Verify OTP">
    </form>
    <?php endif; ?>
</section>
</body>
</html>
