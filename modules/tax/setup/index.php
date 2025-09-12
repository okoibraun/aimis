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

// Fetch all tax configs for current company
$taxes = $conn->query("SELECT * FROM tax_config WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Setup</title>
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
                <h3>Tax - Setup (VAT / GST / WHT)</h3>
              </section>

              <section class="content">
                <?php include("../../../includes/alert.phtml"); ?>

                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-percentage"></i> Tax Configurations
                    </h3>
                    <div class="card-tools">
                      <a href="../" class="btn btn-secondary btn-sm">Back</a>
                      <a href="add.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Tax Rule
                      </a>
                    </div>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped DataTable">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Type</th>
                          <th>Rate (%)</th>
                          <th>Description</th>
                          <th>Status</th>
                          <th>Created</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($taxes as $i => $row): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $row['tax_type'] ?></td>
                            <td><?= number_format($row['rate'], 2) ?></td>
                            <td><?= $row['description'] ?></td>
                            <td>
                              <span class="text text-<?= $row['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                              </span>
                            </td>
                            <td><?= $row['created_at'] ?></td>
                            <td>
                              <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                              </a>
                              <a href="delete.php?id=<?= $row['id'] ?>&tax_type=<?= $row['tax_type'] ?>&rate=<?= $row['rate'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this tax rule?')"><i class="fas fa-trash"></i></a>
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
