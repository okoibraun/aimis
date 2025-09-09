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
  echo "<p>Invalid Quotation ID.</p>";
  exit;
}

$quotation = $conn->query("SELECT * FROM sales_quotations WHERE id = $id")->fetch_assoc();
$customer = $conn->query("SELECT * FROM sales_customers WHERE id = {$quotation['customer_id']}")->fetch_assoc();
$items = $conn->query("SELECT * FROM sales_quotation_items WHERE quotation_id = $id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Quotations</title>
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
            
            <div class="content-wrapper">
                <section class="content-header mt-3 mb-3">
                    <h1>View Quotation</h1>
                </section>
    
                <section class="content">
                    <div class="card-header">
                        <h3 class="card-title">Quotation #<?= htmlspecialchars($quotation['quote_number']) ?></h3>
                        <div class="card-tools">
                            <a href="edit?id=<?= $quotation['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                            <a href="generate_pdf?type=quotation&id=<?= $quotation['id'] ?>" target="_blank" class="btn btn-sm btn-secondary">Export PDF</a>
                            <a href="./" class="btn btn-danger btn-sm">All Quotations</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                        <h4>Customer: <?= htmlspecialchars($customer['name']) ?></h4>
                        <p>
                            Quote Date: <?= $quotation['quotation_date'] ?><br>
                            Expiry Date: <?= $quotation['valid_until'] ?><br>
                            Status: <span class="badge badge-info"><?= $quotation['status'] ?></span>
                        </p>
    
                        <h5>Quotation Items</h5>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Discount</th>
                                <th>Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $index => $item): 
                                $product = $conn->query("SELECT * FROM sales_products WHERE id = {$item['product_id']}")->fetch_assoc();
                                $subtotal = $item['quantity'] * $item['unit_price'] * (1 - $item['discount'] / 100);
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>N<?= number_format($item['unit_price'], 2) ?></td>
                                <td><?= $item['discount'] ?>%</td>
                                <td>N<?= number_format($subtotal, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Tax</strong></td>
                                <td>N<?= number_format($quotation['tax'] ?? 0, 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right"><strong>Total</strong></td>
                                <td>N<?= number_format($quotation['total'], 2) ?></td>
                            </tr>
                            </tfoot>
                        </table>
    
                        <?php if (!empty($quotation['signed_pdf_path'])): ?>
                            <div class="mt-3">
                            <p><strong>Signed PDF:</strong> 
                                <a href="<?= $quotation['signed_pdf_path'] ?>" target="_blank">View Signed Quotation</a>
                            </p>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
            

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
