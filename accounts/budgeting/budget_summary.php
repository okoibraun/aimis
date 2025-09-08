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

$budget_year = $_POST['budget_year'] ?? date('Y');
$company_id = $_SESSION['company_id'];

if(in_array($_SESSION['user_role'], system_users())) {
  // Fetch all budgets for report generation
  $budgets = $conn->query("SELECT * FROM budgets WHERE created_year = $budget_year ORDER BY created_at DESC");
  $total_budget = $conn->query("SELECT SUM(total_amount) AS total_budget FROM budgets WHERE created_year = $budget_year")->fetch_assoc();
} else if(in_array($_SESSION['user_role'], super_roles())) {
  // Fetch all budgets for report generation
  $budgets = $conn->query("SELECT * FROM budgets WHERE company_id = $company_id AND created_year = $budget_year ORDER BY created_at DESC");
  $total_budget = $conn->query("SELECT SUM(total_amount) AS total_budget FROM budgets WHERE company_id = $company_id AND created_year = $budget_year")->fetch_assoc();
} else {
  // Fetch all budgets for report generation
  $budgets = $conn->query("SELECT * FROM budgets WHERE company_id = $company_id AND user_id = $user_id AND created_year = $budget_year ORDER BY created_at DESC");
  $total_budget = $conn->query("SELECT SUM(total_amount) AS total_budget FROM budgets WHERE company_id = $company_id AND user_id = $user_id AND created_year = $budget_year")->fetch_assoc();
}
// $yearly_total = '';

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Budget Summary</title>
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

          <section class="content-header mt-3 mb-3">
            <h1>Budget Summary</h1>
          </section>

          <section class="content">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Budgets</h3>
                <div class="card-tools">
                  <form method="post" class="form-horizontal">
                    <input type="text" name="budget_year" placeholder="Enter year here">
                    <button type="submit" class="btn btn-info">Filter Budget</button>
                    <a href="./" class="btn btn-primary">Back to All Budgets</a>
                  </form>
                </div>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Budget Name</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Total Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($budgets as $budget): ?>
                      <tr>
                        <td><?= htmlspecialchars($budget['budget_name']) ?></td>
                        <td><?= $budget['start_date'] ?></td>
                        <td><?= $budget['end_date'] ?></td>
                        <td>N<?= number_format($budget['total_amount'], 2) ?></td>
                        <td>
                          <a href="budget?id=<?= $budget['id']; ?>" class="btn btn-info">View Budget Details</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td align="right">
                            <strong>Total</strong>
                        </td>
                        <td>
                          <strong><?= $total_budget['total_budget']; ?></strong>
                        </td>
                        <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>

          </section>

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
