<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: ./'); // Already logged in
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
  <body class="bg-body-tertiary">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>AIMIS Cloud Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <form action="auth" method="post">
                            <div class="form-group mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required autofocus>
                            </div>
                            <div class="form-group mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="row mb-4">
                                <div class="col-8">
                                    <!-- <a href="forgot_password.php" class="btn btn-link">I forgot my password</a> -->
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary w-100">Login</button>
                                    </div>
                                </div>
                            </div>
                            <a class="btn btn-link mt-3" href="register">Don't have an account? Register</a>
                        </form>
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
