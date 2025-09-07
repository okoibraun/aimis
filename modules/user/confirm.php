<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/invitation_functions.php';

// if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin', 'system'])) {
//     redirect('../../login.php');
// }

$token = $_GET['token'] ?? '';
$action = $_GET['a'] ?? '';
$invitation = get_invitation_by_token($token);
$errors = [];
$success = false;

if (!$invitation) {
    die('Invalid or expired invitation token.');
}

if(isset($action, $token)) {
  if($action === "ce") {

    $activate_user = $conn->query("UPDATE users SET is_active = 1 AND status='active' WHERE token = '$token'");
    if($activate_user) {
        $success = true;
        $message = "Your account has been activated. You can now log in.";
    } else {
        $errors[] = "Failed to activate your account. Please try again.";
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize_input($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!$full_name || !$password || !$confirm_password) {
        $errors[] = 'All fields are required.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        if (create_user_from_invitation($invitation, $full_name, $hashed_password)) {
            delete_invitation($token);
            $success = true;
        } else {
            $errors[] = 'Failed to create account.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Users</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
          
            <div class="container mt-5">
              <div class="row justify-content-center">
                <div class="col-md-6">

                  <h3>Complete Your Registration</h3>

                  <?php if ($success): ?>
                    <div class="alert alert-success">
                      Registration complete. <a href="../auth/login.php">Login here</a>.
                    </div>
                  <?php else: ?>

                    <?php if (!empty($errors)): ?>
                      <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
                    <?php endif; ?>

                    <form method="post">
                      <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                      </div>

                      <div class="form-group">
                        <label for="password">Choose Password</label>
                        <input type="password" name="password" class="form-control" required>
                      </div>

                      <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                      </div>

                      <button type="submit" class="btn btn-primary">Complete Registration</button>
                    </form>

                  <?php endif; ?>
                </div>
              </div>
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
