<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "view";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
  die("Order ID required.");
}

$orders = $db->query("SELECT o.*, c.company_id, c.email, c.phone
  FROM sales_orders o
  JOIN sales_customers c ON o.customer_id = c.id
  WHERE o.id = $id");

$order = $orders->fetch_assoc();

$items = $db->query("SELECT i.*, p.name AS product_name
  FROM sales_order_items i
  JOIN sales_products p ON i.product_id = p.id
  WHERE i.order_id = $id");
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
            <div class="card">
                <?php if($orders->num_rows > 0) { ?>
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <h3>Order #<?= $order['order_number'] ?></h3>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="orders" class="btn btn-secondary">← Back</a>
                            <a href="orders?action=form&id=<?= $order['id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="orders?action=pdf&id=<?= $order['id'] ?>" class="btn btn-info">Generate PDF</a>
                            <a href="orders?action=sign&id=<?= $order['id'] ?>" class="btn btn-success">Digital Sign</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h5>Customer Info</h5>
                    <p>
                    <strong><?= $order['company_id']; ?></strong><br>
                    Email: <?= $order['email']; ?><br>
                    Phone: <?= $order['phone']; ?>
                    </p>

                    <h5>Order Info</h5>
                    <p>
                    Order Date: <?= $order['order_date'] ?><br>
                    Delivery Date: <?= $order['delivery_date'] ?><br>
                    Status: <span class="badge badge-<?= status_badge($order['status']) ?>"><?= ucfirst($order['status']) ?></span>
                    </p>

                    <h5>Items</h5>
                    <table class="table table-bordered">
                    <thead>
                        <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Discount %</th>
                        <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($items as $item):
                        $subtotal = $item['quantity'] * $item['unit_price'] * (1 - $item['discount_percent'] / 100);
                        $total += $subtotal;
                        ?>
                        <tr>
                        <td><?= $item['product_name'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['unit_price'], 2) ?></td>
                        <td><?= $item['discount_percent'] ?>%</td>
                        <td>$<?= number_format($subtotal, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>

                    <div class="row">
                    <div class="col-md-4 offset-md-8">
                        <p>Subtotal: $<?= number_format($total, 2) ?></p>
                        <p>Tax: $<?= number_format($order['tax_amount'], 2) ?></p>
                        <h4>Total: $<?= number_format($order['total_amount'], 2) ?></h4>
                    </div>
                    </div>

                    <?php if (!empty($order['notes'])): ?>
                    <h5>Notes</h5>
                    <p><?= nl2br($order['notes']) ?></p>
                    <?php endif; ?>
                </div>
                <?php } else { ?>
                <div class="card-body">
                    <div class="alert alert-warning">
                    No order found with ID <?= htmlspecialchars($id) ?>.
                    </div>
                </div>
                <?php } ?>
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
