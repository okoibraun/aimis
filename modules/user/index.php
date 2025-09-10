<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/company_functions.php';
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

//$company_id_filter = $_SESSION['role'] === 'superadmin' ? ($_GET['company_id'] ?? null) : $_SESSION['company_id'];
$company_id_filter = (in_array($_SESSION['role'], system_users())) ? ($_GET['company_id'] ?? null) : $company_id;

$users = get_users_by_company($company_id_filter);
$companies = in_array($_SESSION['role'], system_users()) ? get_all_companies() : [];
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
              <section class="content-header mt-3 mb-3">
                <div class="row">
                  <div class="col-lg-6">
                    <h2>Manage Users</h2>
                  </div>
                  <div class="col-lg-6">
                    <ol class="breadcrumb float-end">
                      <li class="breadcrumb-item"><a href="../../index.php"><i class="fa fa-home"></i> Home</a></li>
                      <li class="breadcrumb-item"><a href="#">Users</a></li>
                    </ol>
                  </div>
                </div>
              </section>

              <section class="content">

                <?php include("../../includes/alert.phtml"); ?>
                
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col-lg-3">
                        <h4 class="align-item-center">Users</h4>
                      </div>
                      <div class="col-lg-5">
                        <div class="float-end">
                          <?php if(in_array($_SESSION['role'], system_users())) { ?>
                            <form method="get" for="company_filter" class="form-inline">
                              <div class="row gy-2 gx-3 align-items-center">
                                <div class="col-auto">
                                  <label for="company_id" class="">Filter by Company:</label>
                                </div>
                                <div class="col-auto">
                                  <select name="company_id" id="company_id" class="form-control select2"  onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <?php foreach($companies as $c) { ?>
                                      <option value="<?= $c['id']; ?>" <?= ($company_id_filter == $c['id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($c['name']); ?>
                                      </option>
                                    <?php } ?>
                                  </select>
                                </div>
                                <div class="col-auto">
                                  <button type="submit" class="btn btn-sm btn-primary mr-2">Filter</button>
                                </div>
                              </div>
                            </form>
                          <?php } ?>                 
                        </div>
                      </div>
                      <div class="col-lg-4 text-end">
                        <a href="create.php" class="btn btn-md btn-primary">Add New User</a>
                        <a href="invite.php" class="btn btn-md btn-primary">Invite a User</a>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover DataTable">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Company</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user) { ?>
                              <tr>
                                <td><?= htmlspecialchars($user['name']); ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td><?= htmlspecialchars($user['role']); ?></td>
                                <td>
                                  <?php $companyid = $user['company_id'] ?? ""; ?>
                                  <?=
                                    ($company = $conn->query("SELECT name FROM companies WHERE id='{$companyid}'")->fetch_assoc()) ? $company['name'] : "none";
                                  ?>
                                </td>
                                <td><?= htmlspecialchars($user['department']); ?></td>
                                <td><?= htmlspecialchars($user['status']); ?></td>
                                <td><?= htmlspecialchars($user['created_at']); ?></td>
                                <td>
                                  <a href="edit.php?id=<?= $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                  <a href="delete.php?id=<?= $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');">Delete</a>
                                </td>
                              </tr>
                            <?php } ?>
                        </tbody>
                      </table>
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
