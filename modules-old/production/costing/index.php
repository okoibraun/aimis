<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$work_orders = mysqli_query($conn, "
    SELECT id, order_code, estimated_cost, actual_cost, cost_variance 
    FROM production_work_orders 
    ORDER BY created_at DESC
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Costing</title>
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
                    <h1>Production Costing Summary</h1>
                </section>
                <section class="content">
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Order Code</th><th>Estimated</th><th>Actual</th><th>Variance</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php while($w = mysqli_fetch_assoc($work_orders)): ?>
                            <tr>
                                <td><?= $w['order_code'] ?></td>
                                <td>₦<?= number_format($w['estimated_cost'], 2) ?></td>
                                <td>₦<?= number_format($w['actual_cost'], 2) ?></td>
                                <td><?= $w['cost_variance'] >= 0 ? '+' : '-' ?>₦<?= number_format(abs($w['cost_variance']), 2) ?></td>
                                <td><a href="breakdown.php?work_order_id=<?= $w['id'] ?>" class="btn btn-sm btn-primary">Breakdown</a></td>
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
