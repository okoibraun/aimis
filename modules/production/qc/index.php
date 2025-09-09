<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$products_tbl = 'inventory_products' ?? 'sales_products';
$records = $conn->query("
    SELECT q.*, pwo.order_code, ip.name AS material_name, u.name AS inspector
    FROM production_qc_checkpoints q
    JOIN production_work_orders pwo ON q.work_order_id = pwo.id
    LEFT JOIN {$products_tbl} ip ON q.material_id = ip.id
    LEFT JOIN users u ON q.inspected_by = u.id
    ORDER BY q.inspected_at DESC
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - QC</title>
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
                    <h1>Quality Control Checkpoints</h1>
                    <a href="create.php" class="btn btn-primary">New Checkpoint</a>
                </section>
                <section class="content">
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Work Order</th><th>Type</th><th>Material</th><th>Result</th><th>Inspector</th><th>Date</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($records)): ?>
                            <tr>
                                <td><?= $row['order_code'] ?></td>
                                <td><?= $row['checkpoint_type'] ?></td>
                                <td><?= $row['material_name'] ?? '-' ?></td>
                                <td><?= $row['result'] ?></td>
                                <td><?= $row['inspector'] ?? 'N/A' ?></td>
                                <td><?= $row['inspected_at'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this QC record?')">Delete</a>
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
