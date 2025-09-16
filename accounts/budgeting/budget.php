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
$page = "view";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}


$budget_id = isset($_GET['id']) ? $_GET['id'] : 0;
$budget = null;

if ($budget_id > 0) {
    if(in_array($_SESSION['user_role'], system_users())) {
      // Fetch budget details by ID
      $budget = $conn->query("SELECT * FROM budgets WHERE id = $budget_id")->fetch_assoc();
      // Fetch Budget Items by budget ID
      $budget_items = $conn->query("SELECT * FROM budget_items WHERE budget_id = $budget_id"); 
    } else if(in_array($_SESSION['user_role'], super_roles())) {
      // Fetch budget details by ID
      $budget = $conn->query("SELECT * FROM budgets WHERE id = $budget_id AND company_id = $company_id")->fetch_assoc();
      // Fetch Budget Items by budget ID
      $budget_items = $conn->query("SELECT * FROM budget_items WHERE budget_id = $budget_id AND company_id = $company_id"); 
    } else {
      // Fetch budget details by ID
      $budget = $conn->query("SELECT * FROM budgets WHERE id = $budget_id AND company_id = $company_id AND user_id = $user_id")->fetch_assoc();
      // Fetch Budget Items by budget ID
      $budget_items = $conn->query("SELECT * FROM budget_items WHERE budget_id = $budget_id AND company_id = $company_id AND user_id = $user_id"); 
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | View Budget</title>
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

          <section class="content-header mt-4 mb-4">
            <div class="card">
              <div class="card-header">
                <h1 class="card-title">View Budget</h1>
                <div class="card-tools">
                  <a href="./" class="btn btn-info">
                    <i class="bi bi-back-arrow"></i> 
                    Back
                  </a>
                </div>
              </div>
            </div>
          </section>

          <section class="content">
            <div class="card">
              <div class="card-body">
                <?php if ($budget): ?>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"><?= htmlspecialchars($budget['budget_name']) ?></h3>
                        </div>
                        <div class="card-body">
                          <p><strong>Start Date:</strong> <?= $budget['start_date'] ?></p>
                          <p><strong>End Date:</strong> <?= $budget['end_date'] ?></p>
                          <p><strong>Total Amount:</strong> N<?= number_format($budget['total_amount'], 2) ?></p>
                        </div>
                      </div>
                    </div>
    
                    <div class="col-md-8">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"><?= $budget['budget_name']; ?> Items</h3>
                        </div>
                        <div class="card-body">
                          <table class="table table-striped DataTables">
                            <thead>
                              <tr>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Discount</th>
                                <th>Total</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach($budget_items as $item) { $product_id = $item['product_id'];?>
                                <tr>
                                  <td><?= $item['budget_item']; ?></td>
                                  <td><?= $item['description']; ?></td>
                                  <td><?= $item['quantity']; ?></td>
                                  <td><?= $item['unit_price']; ?></td>
                                  <td><?= $item['discount']; ?></td>
                                  <td><?= $item['total']; ?></td>
                                </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <div class="alert alert-warning">No budget found.</div>
                <?php endif; ?>
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
