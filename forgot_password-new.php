<?php
session_start();
include('config/db.php');

$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'"));

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $user_id = $user['id'];

        // mysqli_query($conn, "UPDATE users SET token = '$token', reset_requested_at = NOW() WHERE id = $user_id");
        $conn->query("UPDATE users SET token = '$token' WHERE id = $user_id");

        // In production, send by EMAIL
        $reset_link = "/reset_password.php?token=$token";

        $message = "Click here: <a href='$reset_link'>Reset Password</a> to reset your password";
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
  <body class="bg-body-tertiary login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="index.php"><b>AIMIS</b> Cloud</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
            <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="input-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="bi bi-envelope-fill"></span>
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block">Request reset password</button>
                </div>
                <!-- /.col -->
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="login.php">Login</a>
            </p>
            <p class="mb-0">
                <a href="register.php" class="text-center">Register a new membership</a>
            </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    
    <!--begin::Script-->
    <?php include("includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
