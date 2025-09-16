<?php
session_start();
include('config/db.php');

// //Import PHPMailer classes into the global namespace
// //These must be at the top of your script, not inside a function
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;


// function send_reset_password_email($email, $token) {
//     $link = "https://aimiscloud.com.ng/reset_password.php?a=rp&token=" . urlencode($token);
//     //Load Composer's autoloader (created by composer, not included with PHPMailer)
//     require 'vendor/autoload.php';
// }

// $message = '';
// $show_reset_form = 1;

// if($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $email = mysqli_real_escape_string($conn, $_POST['email']);

//     $user = $conn->query("SELECT * FROM users WHERE email = '$email'")->fetch_assoc();

//     if ($user) {
//         $token = bin2hex(random_bytes(50));
//         $user_id = $user['id'];
//         $email = $user['email'];

//         // mysqli_query($conn, "UPDATE users SET token = '$token', reset_requested_at = NOW() WHERE id = $user_id");
//         $update_token = $conn->query("UPDATE users SET token = '$token' WHERE id = $user_id");

//         // Send Email
//         if($update_token) {
//             $send_email = send_reset_password_email($email, $token);

//             if($send_email) {
//                 // Set display message
//                 // $message = "Click here: <a href='$reset_link'>Reset Password</a> to reset your password";
//                 $show_reset_form = 0;
//                 $message = "Check your email that was provided during registration for a password reset link";
//             }

//         }
        
//         // $reset_link = "https://aimiscloud.com.ng/reset_password.php?token=$token";

//     } else {
//         $message = "No account found with that email.";
//     }
// }
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS Cloud | Forgot Password</title>
    <?php include_once("includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="bg-body-tertiary">

  
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Forgot Password</h4>
                    </div>
                    <div class="card-body">
                        <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <?php if($show_reset_form == 1) { ?>
                        <div class="container">
                            <form method="post">
                                <div class="form-group mb-3">
                                    <label class="mb-2">Email Address: </label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-group float-end">
                                    <button type="submit" class="btn btn-primary">Request Reset Link</button>
                                </div>
                            </form>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <p class="text-center mt-3">
                    &copy; <?php echo date('Y'); ?> AIMIS Cloud
                </p>
            </div>
        </div>
    </div>
    <!--begin::Script-->
    <?php include("includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
