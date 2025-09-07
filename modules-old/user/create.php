<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/role_functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin', 'system'])) {
    redirect('../../login.php');
}

$errors = [];
$success = false;

// Superadmin can assign users to any company
$companies = ($_SESSION['role'] === 'system') ? get_all_companies() : [get_company_by_id($_SESSION['company_id'])];

// Get roles based on current user's permission scope
$available_roles = get_available_roles_for_user($_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize_input($_POST['full_name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $role = sanitize_input($_POST['role']);
    $company_id = intval($_POST['company_id']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (!$full_name || !$email || !$role || !$company_id || !$password || !$confirm_password) {
        $errors[] = "All fields are required.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!user_can_manage_company($_SESSION, get_company_by_id($company_id))) {
        $errors[] = "You are not allowed to assign users to this company.";
    }

    if (empty($errors)) {
        if (create_user($full_name, $email, $role, $company_id, $password)) {
            $success = true;
        } else {
            $errors[] = "Failed to create user. Email might already be taken.";
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
          
            <div class="content-wrapper">
              <section class="content-header">
                <div class="container-fluid">
                  <h1>Create New User</h1>
                </div>
              </section>

              <section class="content">
                <div class="container-fluid">

                  <?php if ($success): ?>
                    <div class="alert alert-success">User created successfully.</div>
                  <?php endif; ?>

                  <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
                  <?php endif; ?>

                  <form method="post">
                    <div class="form-group">
                      <label for="full_name">Full Name</label>
                      <input type="text" name="full_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label for="email">Email Address</label>
                      <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label for="company_id">Assign to Company</label>
                      <select name="company_id" class="form-control" required>
                        <?php foreach ($companies as $company): ?>
                          <option value="<?= $company['id']; ?>" <?= ($company['id'] === $_SESSION['company_id']) ? "selected" : "" ?>><?= htmlspecialchars($company['name']); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="role">Role</label>
                      <select name="role" class="form-control" required>
                        <?php foreach ($available_roles as $r): ?>
                          <option value="<?= $r; ?>"><?= ucfirst($r); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="password">Password</label>
                      <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label for="confirm_password">Confirm Password</label>
                      <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Create User</button>
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
