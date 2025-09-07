<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

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

$orders = $db->query("SELECT o.*, c.name AS customer_name, c.email, c.phone
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
    <title>AIMIS | CRM - Orders</title>
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
                <div class="row">
                  <div class="col">
                    <h2 class="">Track Orders <small class="text-muted">Delivery & fullfilments</small></h2>
                  </div>
                  <div class="col-auto text-end">
                    <ol class="breadcrumb float-end">
                      <li class="breadcrumb-item"><a href="/index.php"><i class="fa fa-home"></i> Home</a></li>
                      <li class="breadcrumb-item"><a href="#">CRM</a></li>
                      <li class="breadcrumb-item active">Orders</li>
                    </ol>
                  </div>
                </div>
              </section>

              <section class="content">
                <div class="card">
                    <?php if($orders->num_rows > 0) { ?>
                    <div class="card-header">
                      <h3 class="card-title">
                        <strong>Order #<?= $order['order_number'] ?></strong>
                      </h3>
                      <div class="card-tools">
                          <a href="./" class="btn btn-secondary">← Back</a>
                          <!-- <a href="orders?action=form&id=<?= $order['id'] ?>" class="btn btn-warning">Edit</a>
                          <a href="orders?action=pdf&id=<?= $order['id'] ?>" target="_blank" class="btn btn-info">Generate PDF</a>
                          <a href="orders?action=sign&id=<?= $order['id'] ?>" target="_blank" class="btn btn-success">Digital Sign</a> -->
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row mb-3">
                        <div class="col-3">
                          <h5>Customer Info</h5>
                          <p>
                            <strong><?= $order['customer_name']; ?></strong><br>
                            Email: <?= $order['email']; ?><br>
                            Phone: <?= $order['phone']; ?>
                          </p>
    
                          <h5>Order Info</h5>
                          <p>
                            Order Date: <?= $order['order_date'] ?><br>
                            Delivery Date: <?= $order['delivery_date'] ?><br>
                            <form method="post" class="form-horizontal">
                              <div class="row form-group">
                                <div class="col-3">
                                  <label for="status">Status:</label>
                                </div>
                                <div class="col">
                                  <select name="status" id="" class="form-control border-0">
                                    <?php foreach(['pending', 'confirmed', 'shipped', 'cancelled'] as $status) { ?>
                                      <option value="<?= $status; ?>" <?= $order['status'] == $status ? 'selected' : ''; ?>><?= ucfirst($status); ?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                              </div>
                            </form>
                          </p>
                        </div>
                        <div class="col"></div>
                      </div>
  
                      <div class="row mb-3">
                        <div class="col">
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">Items</h3>
                            </div>
                            <div class="card-body">
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Discount %</th>
                                    <th>Tax Rate %</th>
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
                                    <td>N<?= number_format($item['unit_price'], 2) ?></td>
                                    <td><?= $item['discount_percent'] ?>%</td>
                                    <td><?= $item['tax_rate']; ?>%</td>
                                    <td>N<?= number_format($subtotal, 2) ?></td>
                                  </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
          
                              <div class="row mt-3">
                                <div class="col-md-8">
                                  <?php if (!empty($order['notes'])): ?>
                                  <div class="card">
                                    <div class="card-header">
                                      <h3 class="card-title">Notes</h3>
                                    </div>
                                    <div class="card-body">
                                      <p><?= nl2br($order['notes']) ?></p>
                                    </div>
                                  </div>
                                  <?php endif; ?>
                                </div>
                                <!-- <div class="col-md-4 offset-md-8"> -->
                                <div class="col-md-4">
                                  <table class="table table-bordered">
                                    <tbody>
                                      <tr>
                                        <td>Subtotal: </td>
                                        <td>N<?= number_format($total, 2) ?></td>
                                      </tr>
                                      <tr>
                                        <td>Tax: </td>
                                        <td>N<?= number_format($order['tax_amount'], 2) ?></td>
                                      </tr>
                                      <tr>
                                        <td><h4>Total: </h4></td>
                                        <td><h4>N<?= number_format($order['total_amount'], 2) ?></h4></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                    
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
  
                      <?php if (!empty($order['notes'])): ?>
                      <!-- <div class="row mb-3">
                        <div class="col">
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">Notes</h3>
                            </div>
                            <div class="card-body">
                              <p><?= nl2br($order['notes']) ?></p>
                            </div>
                          </div>
                        </div>
                      </div> -->
                      <?php endif; ?>

                      <div class="row mb-3">
                        <div class="col">
                          <div class="card">
                            <div class="card-header">
                              <h3 class="card-title">Invoices</h3>
                              <div class="card-tools">

                              </div>
                            </div>
                            <div class="card-body">
                              <table class="table table-hover table-striped">
                                <thead>
                                  <tr>
                                    <th>Invoice #</th>
                                    <th>Invoice Date</th>
                                    <th>Due Date</th>
                                    <th>Total (N)</th>
                                    <th>Status</th>
                                  </tr>
                                </thead>
                                <tbody>

                                  <?php $order_id = $order['id']; $invoices = $conn->query("SELECT * FROM sales_invoices WHERE order_id = $order_id"); ?>
                                  <?php foreach ($invoices as $inv): ?>
                                  <tr>
                                    <td><?= $inv['invoice_number'] ?></td>
                                    <td><?= $inv['invoice_date'] ?></td>
                                    <td><?= $inv['due_date'] ?></td>
                                    <td>N<?= number_format($inv['total_amount'], 2) ?></td>
                                    <td>
                                      <span class="text text-<?= match($inv['status']) {
                                        'unpaid' => 'danger',
                                        'partial' => 'warning',
                                        'paid' => 'success',
                                        'overdue' => 'default',
                                        default => 'info'
                                      } ?>"><?= ucfirst($inv['status']) ?></span>
                                    </td>
                                  </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
  
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
