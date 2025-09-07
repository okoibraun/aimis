<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$products_tbl = "inventory_products" ?? "sales_products";
$wo_id = $_GET['id'];
$wo = $conn->query("SELECT * FROM production_work_orders WHERE id = $wo_id")->fetch_assoc();

$materials = mysqli_query($conn, "
    SELECT pri.*, ip.name, ip.standard_cost, (pri.qty_issued * ip.standard_cost) AS cost
    FROM production_requisition_items pri
    JOIN {$products_tbl} ip ON pri.material_id = ip.id
    WHERE requisition_id IN (
        SELECT id FROM production_requisitions WHERE work_order_id = $wo_id
    )
");

$material_total = 0;
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
                <section class="content-header"><h1>Cost Breakdown – Work Order: <?= $wo['order_code'] ?></h1></section>
                <section class="content">
                    <h4>Material Cost</h4>
                    <table class="table table-bordered">
                        <thead><tr><th>Material</th><th>Qty Issued</th><th>Unit Cost</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php while($m = mysqli_fetch_assoc($materials)): 
                                $material_total += $m['cost']; ?>
                                <tr>
                                    <td><?= $m['name'] ?></td>
                                    <td><?= $m['qty_issued'] ?></td>
                                    <td><?= number_format($m['standard_cost'], 2) ?></td>
                                    <td><?= number_format($m['cost'], 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <tr>
                                <th colspan="3">Material Total</th>
                                <th><?= number_format($material_total, 2) ?></th>
                            </tr>
                        </tbody>
                    </table>

                    <?php
                    // Overhead and labor can be entered manually for now
                    $overhead = $material_total * 0.15; // Example 15%
                    $labor = 5000; // Placeholder
                    $total_actual = $material_total + $overhead + $labor;
                    ?>

                    <h4>Other Costs</h4>
                    <table class="table table-striped">
                        <tr><th>Labor Cost</th><td>₦<?= number_format($labor, 2) ?></td></tr>
                        <tr><th>Overhead</th><td>₦<?= number_format($overhead, 2) ?> (15%)</td></tr>
                        <tr><th><strong>Total Actual Cost</strong></th><td><strong>₦<?= number_format($total_actual, 2) ?></strong></td></tr>
                        <tr><th>Planned Cost</th><td>₦<?= number_format($wo['planned_cost'], 2) ?></td></tr>
                        <tr><th>Variance</th><td><strong><?= number_format($wo['planned_cost'] - $total_actual, 2) ?></strong></td></tr>
                    </table>

                    <form method="post" action="update_costs.php">
                        <input type="hidden" name="work_order_id" value="<?= $wo_id ?>">
                        <input type="hidden" name="material_cost" value="<?= $material_total ?>">
                        <input type="hidden" name="labor_cost" value="<?= $labor ?>">
                        <input type="hidden" name="overhead_cost" value="<?= $overhead ?>">
                        <button type="submit" class="btn btn-success">Update Actual Costs</button>
                    </form>
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
