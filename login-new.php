<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Already logged in
    exit();
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS Cloud | Login</title>
    <?php include_once("includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="bg-body-tertiary login-page">
    
    <div class="login-box mt-5">
        <div class="login-logo">
            <a href="index.php"><b>AIMIS</b> Cloud</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form method="post" action="auth.php">
                <div class="input-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="bi bi-envelope-fill"></span>
                    </div>
                </div>
                </div>
                <div class="input-group mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="bi bi-lock-fill"></span>
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <a href="forgot_password.php">I forgot my password</a>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block w-100">Login</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <div class="social-auth-links text-center mb-3">
                <p>- OR -</p>
            </div>
            <!-- /.social-auth-links -->
                
            <p class="mb-0">
                <a href="register.php" class="text-center">Don't have an account? Register</a>
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
