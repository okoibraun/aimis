<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/role_functions.php';
require_once '../../functions/permission_functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin', 'system'])) {
    redirect('../../login.php');
}

$company_id = ($_SESSION['role'] === 'superadmin' && isset($_GET['company_id']))
    ? intval($_GET['company_id'])
    : $_SESSION['company_id'] ?? $_SESSION['company_id'];

// $roles = get_roles_by_company($company_id) ?? 
$get_roles = $conn->query("SELECT * FROM roles WHERE company_id = $company_id");
//$roles = get_all_roles();
$all_permissions = get_all_permissions_by_company($company_id);

$success = false;
$create_success = false;
$assign_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectrole'])) {
  $success = true;
    // $role_id = intval($_POST['role_id']);
    // $selected_permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // if (assign_permissions_to_role($role_id, $selected_permissions)) {
    //     $success = true;
    // }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignPermissionsBtn'])) {
    $role_id = intval($_POST['role_id']);
    $selected_permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    if (assign_permissions_to_role($company_id, $role_id, $selected_permissions)) {
        $assign_success = true;
    }
}

// Create New Permission
if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['addPermissionBtn'])) {
  $permission_name = $_POST['name'];
  $description = $_POST['description'];

  if(create_permission($company_id, $permission_name, $description)) {
    $create_success = true;
  }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Permissions</title>
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
                <h1>Manage Role Permissions</h1>
              </section>

              <section class="content">
                <div class="container-fluid">
                  <?php if ($create_success): ?>
                    <div class="alert alert-success">Permissions created successfully.</div>
                  <?php endif; ?>

                  <?php if(in_array($_SESSION['user_role'], ['system'])) { ?>
                  <div class="row">
                    <form method="post">
                      <div class="card">
                        <div class="card-header">Create New Permission</div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-4">
                              <div class="form-group">
                                <label for="name">Permission Name</label>
                                <input type="text" name="name" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-6">
                              <div class="form-group">
                                <label for="name">Description</label>
                                <input type="text" name="description" class="form-control">
                              </div>
                            </div>
                            <div class="col-2">
                              <div class="form-group">
                                <br>
                                <button name="addPermissionBtn" type="submit" class="btn btn-primary">Create</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                  <?php } ?>

                  <?php if ($success): ?>
                    <div class="alert alert-success mt-5 mb-3">Role selected successfully.</div>
                  <?php endif; ?>
                  <?php if ($assign_success): ?>
                    <div class="alert alert-success mt-5 mb-3">Permissions assigned successfully.</div>
                  <?php endif; ?>

                  <!-- Role Selection Row -->
                  <div class="row <?= (!$success) ? 'mt-5' : ''; ?>">
                    <div class="col-md-4">
                      <form method="post">
                        <input type="hidden" name="selectrole" value="yes">
                        <div class="card">
                          <div class="card-header">
                            <h3 class="card-title">Assign Permissions to Role</h3>
                          </div>
                          <div class="card-body">
                            <div class="form-group">
                              <label for="role_id">Select Role</label>
                              <select name="role_id" class="form-control" required onchange="this.form.submit()">
                                <option value="">-- Select Role --</option>
                                <?php foreach($get_roles as $role) { ?>
                                  <option value="<?= $role['id']; ?>" <?= (isset($_POST['role_id']) && $_POST['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                    <?= $role['name']; ?>
                                  </option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>

                    <div class="col-md-4">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">Permissions</h3>
                        </div>
                        <div class="card-body">
                          <?php if (isset($_POST['role_id'])): 
                            $current_permissions = get_permissions_by_role($_POST['role_id']);
                          ?>

                          <form method="post">
                            <input type="hidden" name="role_id" value="<?= intval($_POST['role_id']) ?>">
                            <div class="form-group">
                              <?php foreach ($all_permissions as $perm): ?>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>"
                                        <?= in_array($perm['id'], $current_permissions) ? 'checked' : '' ?>>
                                  <label class="form-check-label">
                                    <?= htmlspecialchars($perm['name']) ?> (<?= htmlspecialchars($perm['description']) ?>)
                                  </label>
                                </div>
                              <?php endforeach; ?>
                            </div>
                            <div class="form-group mt-3">
                              <button type="submit" class="btn btn-primary" name="assignPermissionsBtn">Save Permissions</button>
                            </div>
                          </form>
        
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4"></div>
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
