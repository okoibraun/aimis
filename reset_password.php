<?php
session_start();
include('config/db.php');

$token = mysqli_real_escape_string($conn, $_GET['token']);
//$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE reset_token = '$token'"));
$user = $conn->query("SELECT * FROM users WHERE token = '$token'")->fetch_assoc();

if (!$user) {
    die('Invalid or expired token.');
}

$show_form = 1;
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // mysqli_query($conn, "UPDATE users SET password='$hashed_password', reset_token=NULL, reset_requested_at=NULL WHERE id=" . $user['id']);
    $conn->query("UPDATE users SET password='$hashed_password', token=NULL WHERE id = '{$user['id']}'");

    $show_form = 0;
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
  <body class="bg-body-tertiary">

  
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Reset Your Password</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <?php if($show_form == 1) { ?>
                        <div class="container">
                            <form method="post">
                                <div class="form-group mb-3">
                                    <label>New Password</label>
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                </div>
                                <button type="submit" class="btn btn-primary">Change Password</button>
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
