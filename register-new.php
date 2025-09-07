<?php
include('config/db.php');
include("./functions/helpers.php");
include("./functions/user_functions.php");

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $token = bin2hex(random_bytes(32));

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Email already registered.";
    } else {
        $insert = $conn->query("INSERT INTO users (name, email, password, role, token) VALUES ('$name', '$email', '$password', '$role', '$token')");

        if ($insert) {
            $message = "Your account has been created, check your email and confirm your email!";
            // Optionally, send a confirmation email or redirect
            $send_email = send_confirmation_email($email, $token);
            if ($send_email) {
                $message .= " Check your email provided for a confirmation email to activate your account.";
            }
            // else {
            //     $message .= " Failed to send confirmation email.";
            // }
        } else {
            $message = "Error registering user.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS Cloud | Register</title>
    <?php include_once("includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="bg-body-tertiary register-page">
    <div class="register-box">
        <div class="register-logo">
            <a href="/"><b>AIMIS</b> Cloud</a>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
            <p class="login-box-msg">Register a new membership</p>

            <?php if ($message): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="input-group mb-3">
                <input type="text" name="name" class="form-control" placeholder="Full name">
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="bi bi-person-fill"></span>
                    </div>
                </div>
                </div>
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
                <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Retype password">
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="bi bi-lock-fill"></span>
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-8">
                    <div class="icheck-primary">
                    <input type="checkbox" id="agreeTerms" class="" name="terms" value="agree">
                    <label for="agreeTerms">
                    I agree to the <a href="#">terms</a>
                    </label>
                    </div>
                </div>
                <input type="hidden" name="role" value="superadmin">
                <!-- /.col -->
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>
                <!-- /.col -->
                </div>
            </form>

            <div class="social-auth-links text-center">
                <p>- OR -</p>
            </div>

            <a href="login.php" class="text-center">I already have an account</a>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    
    <!--begin::Script-->
    <?php include("includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
