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
  <body class="bg-body-tertiary">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h4>Register New User</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>

                        <form action="register" method="post">
                            <div class="form-group mb-3">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <input type="hidden" name="role" value="superadmin">
                            <button type="submit" class="btn btn-success w-100">Register</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="login" class="btn d-block btn-link">Already have an Account? Login</a>
                        </div>
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
