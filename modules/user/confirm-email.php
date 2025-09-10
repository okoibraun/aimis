<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/user_functions.php';

$token = $_GET['token'] ?? '';
$action = $_GET['a'] ?? '';
$errors = [];
$success = false;

if(isset($action, $token)) {
  if($action === "ce") {

    $activate_user = $conn->query("UPDATE users SET is_active='1' AND status='active' WHERE token='$token'");
    if($activate_user) {
        $success = true;
        $message = "Your account has been activated. You can now log in.";
    } else {
        $errors[] = "Failed to activate your account. Please try again.";
    }
  }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Confirm Email</title>
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

                  <h3>Confirm Your Email and Activate Your Account</h3>

                  <?php if ($success): ?>
                    <div class="alert alert-success">
                        <p><?= $message; ?></p>
                      Registration complete. <a href="/login.php">Login here</a>.
                    </div>
                  <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
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
