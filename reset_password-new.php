<?php
session_start();
include('config/db.php');

$token = mysqli_real_escape_string($conn, $_GET['token']);
//$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE reset_token = '$token'"));
$user = $conn->query("SELECT * FROM users WHERE token = '$token'")->fetch_assoc();

if (!$user) {
    die('Invalid or expired token.');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // mysqli_query($conn, "UPDATE users SET password='$hashed_password', reset_token=NULL, reset_requested_at=NULL WHERE id=" . $user['id']);
    $conn->query("UPDATE users SET password='$hashed_password', token=NULL WHERE id = '{$user['id']}'");

    $message = "Password has been reset! You can now <a href='login.php'>login</a>.";
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
            <p class="login-box-msg">You are only one step a way from your new password, recover your password now.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="input-group mb-3">
                    <input type="password" name="new_password" class="form-control" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="confirm_new_password" class="form-control" placeholder="Confirm Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Change password</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <p class="mt-3 mb-1">
                <a href="login.php">Login</a>
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
