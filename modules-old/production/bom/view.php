<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$id = $_GET['id'];
$products_tbl = 'inventory_products' ?? 'sales_products';
$bom = $conn->query("SELECT pb.*, ip.name AS product_name FROM production_bom pb JOIN $products_tbl ip ON pb.product_id = ip.id WHERE pb.id = $id")->fetch_assoc();

$items = $conn->query("SELECT pbi.*, ip.name AS material_name FROM production_bom_items pbi JOIN $products_tbl ip ON pbi.material_id = ip.id WHERE pbi.bom_id = $id");
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
                <section class="content-header"><h1>BOM Details</h1></section>
                <section class="content">
                    <h4>Product: <?= $bom['product_name'] ?></h4>
                    <p>Version: <?= $bom['version'] ?></p>
                    <p>Description: <?= $bom['description'] ?></p>

                    <h4>Materials:</h4>
                    <table class="table table-bordered">
                        <thead><tr><th>Material</th><th>Quantity</th><th>UOM</th></tr></thead>
                        <tbody>
                            <?php while($item = mysqli_fetch_assoc($items)): ?>
                                <tr>
                                    <td><?= $item['material_name'] ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= $item['uom'] ?></td>
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
