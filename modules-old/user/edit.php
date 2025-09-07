<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/role_functions.php';
require_once '../../functions/company_functions.php';

$roles = [
    'admin',
    'superadmin',
    'system'
];

if (!isset($_SESSION['user_id'])) {
    redirect('../../login.php');
}

if (!in_array($_SESSION['role'], $roles)) {
    die('Unauthorized access.');
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = get_user_by_id($user_id);
$errors = [];
$success = false;

// if (!$user || !user_can_manage_company($_SESSION, ['id' => $user['company_id']])) {
//     die("Unauthorized or user not found. {$user['company_id']}");
// }

$available_roles = get_available_roles_for_user($_SESSION);
$companies = ($_SESSION['role'] === 'system') ? get_all_companies() : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize_input($_POST['full_name']);
    $role = sanitize_input($_POST['role']);
    $company_id = ($_SESSION['role'] === 'system') ? intval($_POST['company_id']) : $_SESSION['company_id'];
    $status = sanitize_input($_POST['status']);

    if (!$full_name || !$role) {
        $errors[] = "All fields are required.";
    }

    if (!in_array($role, $available_roles)) {
        $errors[] = "Invalid role.";
    }

    if (empty($errors)) {
        if (update_user($user_id, $full_name, $role, $company_id, $status)) {
            $success = true;
            $user = get_user_by_id($user_id); // refresh
        } else {
            $errors[] = "Failed to update user.";
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
                <h1>Edit User</h1>
              </section>

              <section class="content">
                <div class="container-fluid">
                  <?php if ($success): ?>
                    <div class="alert alert-success">User updated successfully.</div>
                  <?php endif; ?>

                  <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
                  <?php endif; ?>

                  <div class="row">
                    <div class="col-md-4">
                      <div class="card">
                        <div class="card-header border-0">
                          <h3 class="card-title">
                            <i class="bi bi-user"></i>
                            User Info
                          </h3>
                        </div>

                        <div class="card-body">
                          <form method="post">
                            <div class="form-group mb-3">
                              <label>Full Name</label>
                              <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
        
                            <?php if ($_SESSION['role'] === 'system'): ?>
                            <div class="form-group mb-3">
                              <label>Company</label>
                              <select name="company_id" class="form-control">
                                <?php $companies = mysqli_query($conn, "SELECT * FROM companies"); ?>
                                <option value="" selected></option>
                                <?php foreach ($companies as $c): ?>
                                  <option value="<?= $c['id'] ?>" <?= $c['id'] == $user['company_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <?php endif; ?>

                            <div class="row mb-3">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Role</label>
                                  <select name="role" class="form-control">
                                    <?php foreach ($available_roles as $r): ?>
                                      <option value="<?= $r ?>" <?= $r === $user['role'] ? 'selected' : '' ?>>
                                        <?php if ($r === 'system' && $_SESSION['role'] !== 'system') continue; ?>
                                        <?= ucfirst($r) ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                              </div>

                              <div class="col-md-6">
                                <div class="form-group">
                                  <?php $user_status = ['active', 'inactive']; ?>
                                  <label for="status">Status</label>
                                  <select name="status" id="" class="form-control">
                                    <option value="">--- Select ---</option>
                                    <?php foreach($user_status as $status) { ?>
                                      <option value="<?= $status; ?>" <?= $status === $user['status'] ? 'selected' : ''; ?>><?= ucfirst($status); ?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <?php if($success) { ?>
                              <a href="list.php" class="btn btn-danger btn-sm">X Close</a>
                            <?php } ?>
                            <button type="submit" class="btn btn-primary float-end">Update User</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
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
