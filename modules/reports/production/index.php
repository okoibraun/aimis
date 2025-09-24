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

// // Check User Permissions
// $page = "list";
// $user_permissions = get_user_permissions($_SESSION['user_id']);

// if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
//     die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
//     exit;
// }

$filter = $_POST;

$query = "
    SELECT po.*, pwo.order_code, sp.name AS product
    FROM production_output_logs po
    JOIN production_work_orders pwo ON po.work_order_id = pwo.id
    JOIN sales_products sp ON po.product_id = sp.id
    WHERE po.company_id = $company_id AND pwo.company_id = po.company_id AND sp.company_id = po.company_id
";

if(isset($filter['logFilterBtn'], $filter['date_from'])) {
    $date = date('Y-m-d H:i', strtotime($filter['date_from']));
    //$query .= " AND po.produced_at >= DATE({$filter['date_from']})";
    $query .= " AND po.produced_at >= '$date'";
}

if(isset($filter['logFilterBtn'], $filter['date_to'])) {
    $date = date('Y-m-d H:i', strtotime($filter['date_to']));
    //$query .= " AND po.produced_at >= DATE({$filter['date_to']})";
    $query .= " AND po.produced_at <= '$date'";
}

$query .= " ORDER BY po.produced_at DESC";
$logs = $conn->query($query);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Report</title>
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
                    <h1>Production Report</h1>
                </section>

                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Output Logs
                            </h3>
                            <div class="card-tools">
                                <form action="" method="post" class="row">
                                    <div class="col-auto mt-2">
                                        Date From:
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" name="date_from" id="" class="form-control" required>
                                    </div>
                                    <div class="col-auto mt-2">
                                        Date To:
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" name="date_to" id="" class="form-control">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="logFilterBtn" class="btn btn-success">Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-header table-responsive">
                            <table class="table table-bordered DataTable">
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
