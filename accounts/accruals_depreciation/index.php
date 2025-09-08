<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if(in_array($_SESSION['user_role'], system_users())) {
  // Fetch all accruals and depreciation schedules
  $accruals_result = $conn->query("SELECT * FROM accruals");
  $depreciation_result = $conn->query("SELECT * FROM depreciation ORDER BY start_date DESC");
} else if(in_array($_SESSION['user_role'], super_roles())) {
  // Fetch all accruals and depreciation schedules
  $accruals_result = $conn->query("SELECT * FROM accruals WHERE company_id = $company_id");
  $depreciation_result = $conn->query("SELECT * FROM depreciation WHERE company_id = $company_id ORDER BY start_date DESC");
} else {
  // Fetch all accruals and depreciation schedules
  $accruals_result = $conn->query("SELECT * FROM accruals WHERE company_id = $company_id AND user_id = $user_id");
  $depreciation_result = $conn->query("SELECT * FROM depreciation WHERE company_id = $company_id AND user_id = $user_id ORDER BY start_date DESC");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - List Schedules</title>
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
                  <h3>Accrual Schedules</h3>
                </section>

                <section class="content">
                  <!-- Accruals List -->
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Accruals</h3>
                      <div class="card-tools">
                        <a href="schedule_accruals" class="btn btn-primary">Schedule Accruals</a>
                        <!-- <a href="schedule_depreciation" class="btn btn-primary">Schedule Depreciation</a> -->
                      </div>
                    </div>
                    <div class="card-body">
                      <table class="table table-bordered table-hover DataTable">
                        <thead>
                          <tr>
                            <th>Accrual Date</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Account</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($row = mysqli_fetch_assoc($accruals_result)): ?>
                            <tr>
                              <td><?= $row['start_date'] ?></td>
                              <td><?= number_format($row['amount'], 2) ?></td>
                              <td><?= $row['description'] ?></td>
                              <td><?= $conn->query("SELECT account_name FROM accounts WHERE id={$row['account_id']}")->fetch_assoc()['account_name']; ?></td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- Depreciation List -->

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