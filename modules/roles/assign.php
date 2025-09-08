<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/role_functions.php';
require_once '../../functions/user_functions.php';
require_once("../../functions/company_functions.php");

if (!isset($_SESSION['user_id'])) {
    redirect('../../login.php');
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user = get_user_by_id($user_id);

if (!$user || !user_can_manage_company($_SESSION, ['id' => $user['company_id']])) {
    die("Unauthorized or user not found.");
}

$role_q = get_roles_by_company($user['company_id'] ?? 1);
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_name = intval($_POST['role']);
    if (assign_role_to_user($user_id, $role_name)) {
        $success = true;
        $user = get_user_by_id($user_id); // Refresh
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Roles</title>
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
                <h1>Assign Role to User</h1>
              </section>

              <section class="content">
                <div class="container-fluid">

                  <?php if ($success): ?>
                    <div class="alert alert-success">Role updated for user.</div>
                  <?php endif; ?>

                  <form method="post">
                    <div class="form-group">
                      <label>User: <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>) <?= $user['company_id'] ?></label>
                    </div>

                    <div class="form-group">
                      <label for="role_id">Select Role</label>
                      <select name="role" class="form-control" required>
                        <?php foreach ($role_q as $role): ?>
                          <option value="<?= $role['role'] ?>" <?= ($user['role'] == $role['role']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Assign Role</button>
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
