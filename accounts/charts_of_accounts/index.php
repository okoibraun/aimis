<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
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

$company_id = $_SESSION['company_id'];

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts</title>
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
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <!-- Start col -->
                <div class="col-lg-12 connectedSortable">
                    <!-- Page Content -->
                    <section class="content-header mt-3 mb-5">
                      <h1>Chart of Accounts</h1>
                    </section>
                    <section class="content">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">
                            Accounts
                          </h3>
                          <div class="card-tools">
                            <a href="add" class="btn btn-primary">Add New Account</a>
                          </div>
                        </div>
                        <div class="card-body">
                          <table class="table table-bordered table-striped DataTable">
                            <thead>
                              <tr>
                                <th>Account Code</th>
                                <th>Account Name</th>
                                <th>Type</th>
                                <th>Parent Account</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                                  $accounts = $conn->query("SELECT a.*, p.account_name AS parent_name FROM accounts a LEFT JOIN accounts p ON a.parent_account_id = p.id WHERE a.company_id = $company_id");
                                  foreach($accounts as $account) {
                              ?>
                                <tr>
                                  <td><?= $account['account_code'] ?></td>
                                  <td><?= $account['account_name'] ?></td>
                                  <td><?= $account['account_type'] ?></td>
                                  <td><?= ($account['parent_name']) ?? "-" ?></td>
                                  <td>
                                    <a href='edit?id=<?= $account['id'] ?>' class='btn btn-sm btn-warning'>Edit</a>
                                    <a href='delete?id=<?= $account['id'] ?>' class='btn btn-sm btn-danger' onclick='return confirm("Delete this account?")'>Delete</a>
                                  </td>
                                </tr>

                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </section>
                </div>
                <!-- /.Start col -->
            </div>
            <!-- /.row (main row) -->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
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
