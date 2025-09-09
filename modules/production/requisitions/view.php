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
$page = "view";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'];
$req = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT pr.*, pwo.order_code 
    FROM production_requisitions pr
    JOIN production_work_orders pwo ON pr.work_order_id = pwo.id
    WHERE pr.id = $id
"));

$items = mysqli_query($conn, "
    SELECT pri.*, ip.name AS material_name 
    FROM production_requisition_items pri
    JOIN inventory_products ip ON pri.material_id = ip.id
    WHERE requisition_id = $id
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Requisitions</title>
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
                <section class="content-header"><h1>View Requisition</h1></section>
                <section class="content">
                    <table class="table table-bordered">
                        <tr><th>Requisition Code</th><td><?= $req['requisition_code'] ?></td></tr>
                        <tr><th>Work Order</th><td><?= $req['order_code'] ?></td></tr>
                        <tr><th>Created At</th><td><?= $req['created_at'] ?></td></tr>
                    </table>

                    <h4>Material Details</h4>
                    <table class="table table-striped">
                        <thead><tr><th>Material</th><th>Requested</th><th>Issued</th><th>Consumed</th></tr></thead>
                        <tbody>
                            <?php while ($i = mysqli_fetch_assoc($items)): ?>
                            <tr>
                                <td><?= $i['material_name'] ?></td>
                                <td><?= $i['qty_requested'] ?></td>
                                <td><?= $i['qty_issued'] ?></td>
                                <td><?= $i['qty_consumed'] ?></td>
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
