<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($user_id)) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
// $page = "view";
// $user_permissions = get_user_permissions($user_id);

// if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
//     die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
//     exit;
// }

$id = isset($_GET['id']) ? $_GET['id'] : '';
$products_tbl = 'sales_products';

$wo = $conn->query("SELECT pwo.*, ip.name AS product_name, pb.version AS bom_version
          FROM production_work_orders pwo
          JOIN $products_tbl ip ON pwo.product_id = ip.id
          LEFT JOIN production_bom pb ON pwo.bom_id = pb.id
          WHERE pwo.id = $id")->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Work Orders</title>
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
                  <h1>Work Order #<?= $wo['order_code'] ?></h1>
                </section>

                <section class="content">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Work Order Details</h3>
                      <div class="card-tools">
                        <a href="./" class="btn btn-danger btn-sm">X</a>
                      </div>
                    </div>
                    <div class="card-body table-responsive">
                      <table class="table table-striped">
                          <tr><th>Product</th><td><?= $wo['product_name'] ?></td></tr>
                          <tr><th>Quantity</th><td><?= $wo['quantity'] ?></td></tr>
                          <tr><th>BOM Version</th><td><?= $wo['bom_version'] ?? 'N/A' ?></td></tr>
                          <tr><th>Status</th><td><?= $wo['status'] ?></td></tr>
                          <tr><th>Scheduled Start</th><td><?= $wo['scheduled_start'] ?></td></tr>
                          <tr><th>Scheduled End</th><td><?= $wo['scheduled_end'] ?></td></tr>
                          <tr><th>Created At</th><td><?= $wo['created_at'] ?></td></tr>
                      </table>
                      <div class="card mt-3 mb-3">
                        <div class="card-header">
                          <h3 class="card-title">Assigned Resources</h3>
                        </div>
                        <div class="card-body table-responsive">
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th>Resource</th>
                                <th>Start</th>
                                <th>end</th>
                                <th>Shift</th>
                                <th>Remark</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $assigned_resources = $conn->query("
                                SELECT pra.*, pr.name AS resource_name
                                FROM production_resource_assignments pra
                                JOIN production_resources pr ON pr.id = pra.resource_id
                                WHERE pra.work_order_id = {$wo['id']} AND pra.company_id = $company_id");
                              ?>
                              <?php foreach($assigned_resources as $ar) { ?>
                              <tr>
                                <td><?= $ar['resource_name'] ?></td>
                                <td><?= $ar['assigned_start'] ?></td>
                                <td><?= $ar['assigned_end'] ?></td>
                                <td><?= $ar['shift'] ?></td>
                                <td><?= $ar['remarks'] ?></td>
                              </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
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
