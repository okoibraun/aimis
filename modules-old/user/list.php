<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/company_functions.php';

//if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin', 'system'])) {
if(!isset($_SESSION['user_id'])) {
    redirect('../../login.php');
}

//$company_id_filter = $_SESSION['role'] === 'superadmin' ? ($_GET['company_id'] ?? null) : $_SESSION['company_id'];
$company_id_filter = ($_SESSION['role'] === "system") ? ($_GET['company_id'] ?? null) : $_SESSION['company_id'];

$roles = [
    'admin',
    'superadmin',
    'system'
];
$users = get_users_by_company($company_id_filter);
$companies = in_array($_SESSION['role'], $roles) ? get_all_companies() : [];
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

                <div class="">
                  <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                  <?php endif; ?>
                </div>
                
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col-lg-3">
                        <h4 class="align-item-center">Users</h4>
                      </div>
                      <div class="col-lg-5">
                        <div class="float-end">
                          <?php if ($_SESSION['role'] === 'system'): ?>
                            <form method="get" for="company_filter" class="form-inline">
                              <div class="row gy-2 gx-3 align-items-center">
                                <div class="col-auto">
                                  <label for="company_id" class="">Filter by Company:</label>
                                </div>
                                <div class="col-auto">
                                  <select name="company_id" id="company_id" class="form-control select2"  onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <?php foreach ($companies as $c): ?>
                                      <option value="<?= $c['id']; ?>" <?= ($company_id_filter == $c['id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($c['name']); ?>
                                      </option>
                                    <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="col-auto">
                                  <button type="submit" class="btn btn-sm btn-primary mr-2">Filter</button>
                                </div>
                              </div>
                            </form>
                          <?php endif; ?>                 
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
                      <table class="table table-bordered table-hover">
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
                            <?php foreach ($users as $user): ?>
                              <tr>
                                <td><?= htmlspecialchars($user['name']); ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td><?= htmlspecialchars($user['role']); ?></td>
                                <td>
                                  <?php $companyid = $user['company_id'] ?? ""; ?>
                                  <?=
                                    ($company = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM companies WHERE id='{$companyid}'"))) ? $company['name'] : "none";
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
                            <?php endforeach; ?>
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
