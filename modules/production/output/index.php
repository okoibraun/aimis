<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
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

$products_tbl = 'inventory_products' ?? 'sales_products';
$logs = mysqli_query($conn, "
    SELECT po.*, pwo.order_code, ip.name AS product
    FROM production_output_logs po
    JOIN production_work_orders pwo ON po.work_order_id = pwo.id
    JOIN {$products_tbl} ip ON po.product_id = ip.id
    ORDER BY po.produced_at DESC
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - BOM</title>
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
                <section class="content-header">
                    <h1>Production Output Logs</h1>
                    <a href="create.php" class="btn btn-primary">Log Output</a>
                </section>

                <section class="content">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Work Order</th>
                                <th>Product</th>
                                <th>Produced</th>
                                <th>Defective</th>
                                <th>Batch</th>
                                <th>Date</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($l = mysqli_fetch_assoc($logs)): ?>
                                <tr>
                                    <td><?= $l['order_code'] ?></td>
                                    <td><?= $l['product'] ?></td>
                                    <td><?= $l['quantity_produced'] ?></td>
                                    <td><?= $l['quantity_defective'] ?></td>
                                    <td><?= $l['batch_number'] ?></td>
                                    <td><?= date('Y-m-d H:i', strtotime($l['produced_at'])) ?></td>
                                    <td><?= $l['remarks'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
