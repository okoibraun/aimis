<?phpsession_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$products_tbl = 'inventory_products' ?? 'sales_products';

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
                <section class="content-header"><h1>Work Order #<?= $wo['order_code'] ?></h1></section>
                <section class="content">
                    <table class="table table-striped">
                        <tr><th>Product</th><td><?= $wo['product_name'] ?></td></tr>
                        <tr><th>Quantity</th><td><?= $wo['quantity'] ?></td></tr>
                        <tr><th>BOM Version</th><td><?= $wo['bom_version'] ?? 'N/A' ?></td></tr>
                        <tr><th>Status</th><td><?= $wo['status'] ?></td></tr>
                        <tr><th>Scheduled Start</th><td><?= $wo['scheduled_start'] ?></td></tr>
                        <tr><th>Scheduled End</th><td><?= $wo['scheduled_end'] ?></td></tr>
                        <tr><th>Created At</th><td><?= $wo['created_at'] ?></td></tr>
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
.php'; ?>
