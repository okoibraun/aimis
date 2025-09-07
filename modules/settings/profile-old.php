<?php
require_once '../../config/db.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/user_functions.php';

// ensure_logged_in();
$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    $errors = [];

    if ($password && $password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        update_user_profile($user_id, $name, $email, $password);
        $user = get_user_by_id($user_id); // Refresh after update
        $success = "Profile updated successfully.";
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Settings</title>
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
          
            <div class="content-wrapper">
              <section class="content-header">
                <h1>My Profile</h1>
              </section>
              <section class="content">
                <div class="card card-primary">
                  <div class="card-header"><h3 class="card-title">Update Profile</h3></div>
                  <form method="POST">
                    <div class="card-body">
                      <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
                      <?php elseif (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                      <?php endif; ?>

                      <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                      </div>
                      <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                      </div>
                      <div class="form-group">
                        <label>New Password (optional)</label>
                        <input type="password" name="password" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control">
                      </div>
                    </div>
                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                  </form>
                </div>
              </section>
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
