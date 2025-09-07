<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$products_tbl = 'inventory_products' ?? 'sales_products';
$result = $conn->query("SELECT pb.id, ip.name AS product_name, pb.version, pb.created_at
        FROM production_bom pb
        JOIN $products_tbl ip ON pb.product_id = ip.id");
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
                    <h1>Bill of Materials</h1>
                    <a href="create.php" class="btn btn-primary">Add New BOM</a>
                </section>
                <section class="content">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th><th>Product</th><th>Version</th><th>Created At</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= $row['product_name'] ?></td>
                                    <td><?= $row['version'] ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                          onclick="return confirm('Delete this BOM?')">Delete</a>
                                    </td>
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
