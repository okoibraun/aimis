<?php
// session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
// include('../config/db.php');

// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit();
// }

require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/auth_functions.php';

// Restrict access
// if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
//     redirect('../auth/login.php');
// }

// Get companies to show
if ($_SESSION['role'] === 'admin') {
    $companies = get_all_companies();
} else {
    $companies = get_companies_by_group($_SESSION['company_id']);
}

$companies = get_all_companies();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Companies</title>
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
                <section class="content-header mt-4">
                  <div class="container-fluid">
                    <h1>Company List</h1>
                  </div>
                </section>

                <section class="content">
                  <div class="container-fluid">
                    <a href="create.php" class="btn btn-primary mb-3 float-end">Add New Company</a>

                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>S/N</th>
                          <th>Company Name</th>
                          <th>Industry</th>
                          <th>Is Parent</th>
                          <th>Created At</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php $sn=0; foreach ($companies as $company): $sn++?>
                        <tr>
                          <td><?= $sn; ?></td>
                          <td><?= htmlspecialchars($company['name']); ?></td>
                          <td><?= $company['industry'] ?? '—'; ?></td>
                          <td><?= ($company['is_parent'] == 1) ? "Yes" : "No"; ?></td>
                          <td><?= $company['created_at']; ?></td>
                          <td>
                            <a href="edit.php?id=<?= $company['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $company['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                      </tbody>
                    </table>
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
