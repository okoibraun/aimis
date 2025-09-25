<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if(in_array($_SESSION['user_role'], system_users())) {
  $orders = $db->query("
    SELECT o.*, c.company_id, c.name
    FROM sales_orders o 
    JOIN sales_customers c ON o.customer_id = c.id
    ORDER BY o.order_date ASC
  ");
} else {
  $orders = $db->query("
    SELECT o.*, c.company_id, c.name
    FROM sales_orders o 
    JOIN sales_customers c ON o.customer_id = c.id
    WHERE o.company_id = $company_id
    ORDER BY o.order_date ASC
  ");
}
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
                  <h4 class="card-title">Sales Orders</h4>
                  <div class="card-tools">
                    <a href="orders?action=form" class="btn btn-primary">New Order</a>
                  </div>
                </div>
                <div class="card-body">
                  <table class="table table-bordered table-striped DataTable">
                    <thead>
                      <tr>
                        <th>Order Date</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Total (N)</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($orders as $order): ?>
                      <tr>
                        <td><?= $order['order_date'] ?></td>
                        <td><?= $order['order_number'] ?></td>
                        <td><?= $order['name'] ?></td>
                        <td class="text text-<?= 
                        match($order['status']) {
                          'pending' => 'info',
                          'confirmed' => 'success',
                          'shipped' => 'primary',
                          'cancelled' => 'danger',
                          default => 'warning'
                        } ?>"><?= ucfirst($order['status']) ?></td>
                        <td><?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                          <a href="order?id=<?= $order['id']; ?>" class="btn btn-info btn-xs" title="View Order">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="orders?action=form&id=<?= $order['id'] ?>" class="btn btn-xs btn-primary" title="Edit Order">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a href="../invoices/add.php?oid=<?= $order['id'] ?>" class="btn btn-xs btn-warning" title="Create Invoice for this Order">
                            <i class="fas fa-calculator"></i>
                          </a>
                          <a href="orders?action=delete&id=<?= $order['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this order?')" title="Delete Order">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
            </div>
          </section>
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
