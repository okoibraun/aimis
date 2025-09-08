<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
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
  $quotations = get_all_rows('sales_quotations', 'quotation_date DESC');
} else {
  $quotations = $conn->query("SELECT * FROM sales_quotations WHERE company_id = $company_id ORDER BY quotation_date DESC");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Quotations</title>
    <?php include_once("../../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
            

          <section class="content mt-5">
            <div class="card card-default">
                <div class="card-header">
                  <div class="row">
                    <div class="col-md-6">
                      <h4>Quotations</h4>
                    </div>
                    <div class="col-md-6 text-end">
                      <a href="add" class="btn btn-primary">Create Quotation</a>
                    </div>
                  </div>
                </div>
              <div class="card-body table-responsive">
                <table class="table table-hover table-striped DataTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Quote No.</th>
                      <th>Customer</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Total (N)</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($quotations as $quote): ?>
                      <?php $customer = $conn->query("SELECT * FROM sales_customers WHERE id = {$quote['customer_id']}")->fetch_assoc(); //get_row_by_id('sales_customers', $quote['customer_id']); ?>
                      <tr>
                        <td><?= $quote['id'] ?></td>
                        <td><?= $quote['quote_number'] ?></td>
                        <td><?= htmlspecialchars($customer['name']) ?></td>
                        <td><?= $quote['quotation_date'] ?></td>
                        <td><span class="text text-primary"><?= $quote['status'] ?></span></td>
                        <td>N<?= number_format($quote['total'], 2) ?></td>
                        <td>
                          <a href="quotation?id=<?= $quote['id'] ?>" class="btn btn-xs btn-info">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="edit?id=<?= $quote['id'] ?>" class="btn btn-xs btn-primary">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a href="form?action=delete&id=<?= $quote['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this quotation?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

          <?php include("_modal.php"); ?>
        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
