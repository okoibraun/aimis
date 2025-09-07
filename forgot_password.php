<?php
session_start();
include('config/db.php');

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function send_reset_password_email($email, $token) {
    $link = "https://aimiscloud.com.ng/reset_password.php?a=rp&token=" . urlencode($token);
    //Load Composer's autoloader (created by composer, not included with PHPMailer)
    require 'vendor/autoload.php';

//     //Create a new PHPMailer instance
// $mail = new PHPMailer();
// //Tell PHPMailer to use SMTP
// $mail->isSMTP();
// //Enable SMTP debugging
// //SMTP::DEBUG_OFF = off (for production use)
// //SMTP::DEBUG_CLIENT = client messages
// //SMTP::DEBUG_SERVER = client and server messages
// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
// //Set the hostname of the mail server
// $mail->Host = 'aimiscloud.com.ng';
// //Set the SMTP port number - likely to be 25, 465 or 587
// $mail->Port = 465;
// //Whether to use SMTP authentication
// $mail->SMTPAuth = true;
// //Username to use for SMTP authentication
// $mail->Username = 'no-reply@aimiscloud.com.ng';
// //Password to use for SMTP authentication
// $mail->Password = 'C0n50l3##';
// //Set who the message is to be sent from
// $mail->setFrom('no-reply@aimiscloud.com.ng', 'AIMIS Cloud');
// //Set an alternative reply-to address
// //$mail->addReplyTo('replyto@example.com', 'First Last');
// //Set who the message is to be sent to
// $mail->addAddress($email);
// //Set the subject line
// $mail->Subject = 'Reset Password Email!';
// //Read an HTML message body from an external file, convert referenced images to embedded,
// //convert HTML into a basic plain-text alternative body
// $mail->msgHTML(file_get_contents('email.php'), __DIR__);
// //Replace the plain text body with one created manually
// $mail->AltBody = 'This is a plain-text message body';
// //Attach an image file
// //$mail->addAttachment('images/phpmailer_mini.png');

// //SMTP XCLIENT attributes can be passed with setSMTPXclientAttribute method
// //$mail->setSMTPXclientAttribute('LOGIN', 'yourname@example.com');
// //$mail->setSMTPXclientAttribute('ADDR', '10.10.10.10');
// //$mail->setSMTPXclientAttribute('HELO', 'test.example.com');

// //send the message, check for errors
// if (!$mail->send()) {
//     echo 'Mailer Error: ' . $mail->ErrorInfo;
// } else {
//     echo 'Message sent!';
// }
    
    // //Create an instance; passing `true` enables exceptions
    // $mail = new PHPMailer(true);
    // try {
    //     //Server settings
    //     $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    //     $mail->isSMTP();                                            //Send using SMTP
    //     $mail->Host       = 'aimiscloud.com.ng';                     //Set the SMTP server to send through
    //     $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    //     $mail->Username   = 'no-reply@aimiscloud.com.ng';                     //SMTP username
    //     $mail->Password   = 'C0n50l3##';                               //SMTP password
    //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    //     $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //     //Recipients
    //     $mail->setFrom('no-reply@aimiscloud.com.ng', 'AIMIS Cloud');
    //     //$mail->addAddress($email, 'User');     //Add a recipient
    //     $mail->addAddress($email); //Recipient
    //     //$mail->addAddress('ellen@example.com');               //Name is optional
    //     //$mail->addReplyTo('info@example.com', 'Information');
    //     //$mail->addCC('cc@example.com');
    //     //$mail->addBCC('bcc@example.com');

    //     //Attachments
    //     //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //     //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //     //Content
    //     $mail->isHTML(true);                                  //Set email format to HTML
    //     $mail->Subject = 'Reset Password Email!';
    //     //$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    //     $mail->body = "";
    //     $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    //     return $mail->send();
    //     //echo 'Message has been sent';
    // } catch (Exception $e) {
    //     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    // }

    // $subject = "Reset Password Email!";
    // $link = "https://aimiscloud.com.ng/reset_password.php?a=rp&token=" . urlencode($token);

    // $message = "
    // <html>
    // <head>
    //   <title>Reset Password Email</title>
    // </head>
    // <body style='font-family: Arial, sans-serif;'>
    //   <h4 style='color: #333;'>Password Reset Request for Email: {$email}</h4>
    //   <p>Hello {$email},</p>
    //   <p>If you've lost your password or wish to reset it, use the link below to get started:</p>
    //   <p style='text-align: center; margin: 20px;'>
    //     <a href='{$link}' style='padding: 10px 20px; background-color: #007BFF; color: #fff; text-decoration: none; border-radius: 5px;'>Reset your password</a>
    //   </p>
    //   <p>If you did not request this, you can safely ignore this email.</p>
    // </body>
    // </html>";

    // $headers = "MIME-Version: 1.0\r\n";
    // $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    // $headers .= "From: AIMIS Cloud <no-reply@aimiscloud.com.ng>";

    // return mail($email, $subject, $message, $headers);


}

$message = '';
$show_reset_form = 1;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $user = $conn->query("SELECT * FROM users WHERE email = '$email'")->fetch_assoc();

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $user_id = $user['id'];
        $email = $user['email'];

        // mysqli_query($conn, "UPDATE users SET token = '$token', reset_requested_at = NOW() WHERE id = $user_id");
        $update_token = $conn->query("UPDATE users SET token = '$token' WHERE id = $user_id");

        // Send Email
        if($update_token) {
            $send_email = send_reset_password_email($email, $token);

            if($send_email) {
                // Set display message
                // $message = "Click here: <a href='$reset_link'>Reset Password</a> to reset your password";
                $show_reset_form = 0;
                $message = "Check your email that was provided during registration for a password reset link";
            }

        }
        
        // $reset_link = "https://aimiscloud.com.ng/reset_password.php?token=$token";

    } else {
        $message = "No account found with that email.";
    }
}
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
