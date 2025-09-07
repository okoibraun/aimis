<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/role_functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('../../login.php');
}

if(!in_array($_SESSION['role'], ['admin', 'superadmin', 'system'])) {
  $_SESSION['message'] = "You are not authorized for this action";
}

// $company_id = (in_array($_SESSION['role'], ['superadmin', 'system'])) ? intval($_GET['company_id']) : $_SESSION['company_id'];
$company_id = (in_array($_SESSION['role'], ['system'])) ? "" : $_SESSION['company_id'];
// $roles = get_roles_by_company($company_id);
// $roles = get_all_roles();
$role_q = ($_SESSION['role'] === "system") ? get_all_roles() : get_roles_by_company($company_id);
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyid = $_SESSION['company_id'];
    $role_name = sanitize_input($_POST['role_name']);
    $role = sanitize_input($_POST['role']);
    $description = sanitize_input($_POST['description']);

    if (!$role_name) {
        $errors[] = "Role name is required.";
    }

    if (empty($errors)) {
        if (create_role($companyid, $role, $role_name, $description)) {
            //$roles = get_roles_by_company($company_id); // refresh list
            // $roles = get_all_roles();
            $role_q = ($_SESSION['role'] === "system") ? get_all_roles() : get_roles_by_company($company_id);
            $success = true;
        } else {
            $errors[] = "Role creation failed. Might already exist.";
        }
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
                <h1>Manage Roles</h1>
              </section>

              <section class="content mt-5">
                <div class="row">

                  <?php if ($success): ?>
                    <div class="alert alert-success">Role created successfully.</div>
                  <?php endif; ?>

                  <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
                  <?php endif; ?>

                  <?php if(in_array($_SESSION['user_role'], ['system'])) { ?>
                  <div class="col-lg-6">
                    <div class="card">
                      <div class="card-header">Add New Role</div>
                      <div class="card-body">
                        <form method="post" class="mb-4">
                          <div class="row">
                            <div class="col-6">
                              <div class="form-group">
                                <label for="role_name">Role Alias</label>
                                <input type="text" name="role" class="form-control" placeholder="role" required>
                              </div>
                            </div>
                            <div class="col-6">
                              <div class="form-group">
                                <label for="">Role Title</label>
                                <input type="text" name="role_name" id="" class="form-control" placeholder="Role Title" required>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="" cols="30" rows="5" class="form-control"></textarea>
                          </div>
  
                          <button type="submit" class="btn btn-primary float-end">Add Role</button>
                        </form>
                      </div>
                    </div>
                  </div>
                  <?php } ?>

                  <div class="col-lg-6">
                    <div class="card">
                      <div class="card-header">
                        <h3 class="card-title">Roles</h3>
                      </div>
                      <div class="card-body">
                        <table class="table" id="zero-config">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Role</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach($role_q as $role) { ?>
                              <tr>
                                <td><?= $role['id'] ?></td>
                                <td><?= htmlspecialchars($role['name']) ?> [<?= htmlspecialchars($role['role']) ?>]</td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
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
