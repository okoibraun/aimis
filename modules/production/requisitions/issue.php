<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}


$req_id = $_GET['id'];

$items = mysqli_query($conn, "
    SELECT pri.*, ip.name, ip.current_stock
    FROM production_requisition_items pri
    JOIN inventory_products ip ON pri.material_id = ip.id
    WHERE requisition_id = $req_id
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Downtime</title>
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
                    <h1>Issue Materials - Requisition #<?= $req_id ?></h1>
                </section>
                <section class="content">
                    <form action="issue_save.php" method="post">
                        <input type="hidden" name="requisition_id" value="<?= $req_id ?>">

                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Material</th><th>Requested</th><th>Available</th><th>Qty to Issue</th></tr>
                            </thead>
                            <tbody>
                                <?php while($item = mysqli_fetch_assoc($items)): ?>
                                <tr>
                                    <td><?= $item['name'] ?></td>
                                    <td><?= $item['qty_requested'] ?></td>
                                    <td><?= $item['current_stock'] ?></td>
                                    <td>
                                        <input type="number" step="0.01" name="issued[<?= $item['id'] ?>]" max="<?= $item['current_stock'] ?>" class="form-control" value="<?= $item['qty_requested'] ?>">
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-success">Confirm Issue & Deduct Stock</button>
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
