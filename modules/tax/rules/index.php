<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// Fetch existing rules
$rules = $conn->query("SELECT * FROM intl_tax_rules WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Rules</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
              <section class="content-header mt-3 mb-3">
                <h3>Tax - Rules</h3>
              </section>

              <section class="content">
                <?php include("../../../includes/alert.phtml"); ?>
                
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      <!-- <i class="fas fa-globe"></i> Country-Specific Tax Templates & BEPS Compliance -->
                       Rules
                    </h3>
                    <div class="card-tools">
                      <a href="../" class="btn btn-secondary btn-sm">Back</a>
                      <a href="add.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Rule
                      </a>
                    </div>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Country</th>
                          <th>Template</th>
                          <th>BEPS Compliant</th>
                          <th>Status</th>
                          <th>Updated</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($rules as $i => $r): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $r['country'] ?></td>
                            <td><?= $r['template_name'] ?></td>
                            <td>
                              <?= $r['beps_compliant'] ? '<span class="text text-success">Yes</span>' : '<span class="text text-warning">No</span>' ?>
                            </td>
                            <td>
                              <span class="text text-<?= $r['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $r['is_active'] ? 'Active' : 'Inactive' ?>
                              </span>
                            </td>
                            <td><?= $r['updated_at'] ?></td>
                            <td>
                              <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                              </a>
                              <a href="delete.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this rule?')">
                                <i class="fas fa-trash"></i>
                              </a>
                            </td>
                          </tr>
                        <?php endforeach ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </section>
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
