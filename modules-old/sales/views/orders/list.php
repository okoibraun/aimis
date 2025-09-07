<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$orders = $db->query("
  SELECT o.*, c.company_id, c.name
  FROM sales_orders o 
  JOIN sales_customers c ON o.customer_id = c.id
  ORDER BY o.order_date DESC
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Orders</title>
    <?php include_once("../../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
            

          <section class="content mt-5">
            <div class="card card-default">
                <div class="card-header">
                  <div class="row">
                    <div class="col-md-6">
                      <h4>Sales Orders</h4>
                    </div>
                    <div class="col-md-6 text-end">
                      <a href="orders.php?action=form" class="btn btn-primary">New Order</a>
                    </div>
                  </div>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-bordered table-striped datatable">
                    <thead>
                      <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Total ($)</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($orders as $order): ?>
                      <tr>
                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                        <td><?= htmlspecialchars($order['name']) ?></td>
                        <td><?= $order['order_date'] ?></td>
                        <td><?= ucfirst($order['status']) ?></td>
                        <td><?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                          <a href="orders.php?action=form&id=<?= $order['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                          <a href="orders.php?action=delete&id=<?= $order['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?')">Delete</a>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
            </div>
          </section>

          <?php include("_modal.php"); ?>
        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
