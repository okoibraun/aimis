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


$invoice_id = intval($_GET['id'] ?? 0);
$invoice = null;
$payments = [];
$total_paid = 0;

if ($invoice_id) {
  if(in_array($_SESSION['user_role'], system_users())) {
    // Get invoice details
    $invoice = $conn->query("SELECT *FROM sales_invoices WHERE id = $invoice_id")->fetch_assoc();

    // Get payments
    $payments_result = $conn->query("SELECT * FROM sales_invoice_payments WHERE invoice_id = $invoice_id ORDER BY payment_date ASC");
    foreach($payments_result as $payment) {
        $payments[] = $payment;
        $total_paid += $payment['amount'];
    }
  } else if(in_array($_SESSION['user_role'], super_roles())) {
    // Get invoice details
    $invoice = $conn->query("SELECT *FROM sales_invoices WHERE id = $invoice_id AND company_id = $company_id")->fetch_assoc();

    // Get payments
    $payments_result = $conn->query("SELECT * FROM sales_invoice_payments WHERE invoice_id = $invoice_id ORDER BY payment_date ASC");
    foreach($payments_result as $payment) {
        $payments[] = $payment;
        $total_paid += $payment['amount'];
    }
  } else {
    // Get invoice details
    $invoice = $conn->query("SELECT * FROM sales_invoices WHERE id = $invoice_id AND company_id = $company_id AND user_id = $user_id OR employee_id = $employee_id")->fetch_assoc();

    //$invoice = $conn->query("SELECT * FROM invoices WHERE id = $invoice_id")->fetch_assoc();

    // Get payments
    $payments_result = $conn->query("SELECT * FROM sales_invoice_payments WHERE invoice_id = $invoice_id AND company_id = $company_id AND user_id = $user_id OR employee_id = $employee_id ORDER BY payment_date ASC");
    foreach($payments_result as $payment) {
        $payments[] = $payment;
        $total_paid += $payment['amount'];
    }
  }
}

$balance = $invoice ? ($invoice['total_amount'] - $total_paid) : 0;
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | View Invoice</title>
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

          <section class="content-header mt-3 mb-3">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Invoice Details</h3>
                <div class="card-tools">
                  <a href="./" class="btn btn-info">Back</a>
                </div>
              </div>
            </div>
          </section>

          <section class="content">
          <?php if ($invoice): ?>
            <div class="row">
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Invoice</h3>
                    <!-- <div class="card-tools">
                      <a href="payments?invoice_id=<?= $invoice['id']; ?>" class="btn btn-success btn-sm">Pay</a>
                    </div> -->
                  </div>
                  <div class="card-body">
                    <h4>Invoice #: <?= $invoice['invoice_number'] ?></h4>
                    <p><strong>Customer:</strong> <?= ($invoice['customer_id']) ? $conn->query("SELECT name FROM sales_customers WHERE id = {$invoice['customer_id']}")->fetch_assoc()['name'] : '-' ?></p>
                    <p><strong>Date:</strong> <?= $invoice['invoice_date'] ?></p>
                    <p><strong>Due:</strong> <?= $invoice['due_date'] ?></p>
                    <p><strong>Description:</strong> <?= nl2br($invoice['notes']) ?></p>
                    <p><strong>Total Amount:</strong> <?= number_format($invoice['total_amount'], 2) ?></p>
                    <p><strong>Paid:</strong> <?= number_format($total_paid, 2) ?></p>
                    <p><strong>Balance:</strong> <?= number_format($balance, 2) ?></p>
    
                    <?php if ($balance > 0): ?>
                      <a href="payments?invoice_id=<?= $invoice_id ?>" class="btn btn-success">
                        Record Payment
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <div class="col-md-8">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Payment History</h3>
                  </div>
                  <div class="card-body">
                    <?php if (count($payments) > 0): ?>
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($payments as $p): ?>
                            <tr>
                              <td><?= $p['payment_date'] ?></td>
                              <td><?= number_format($p['amount'], 2) ?></td>
                              <td><?= $p['payment_method'] ?></td>
                              <td><?= $p['reference'] ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <p>No payments recorded yet.</p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

          <?php else: ?>
            <div class="row">
              <div class="col-lg-12 col-md-12">
                <div class="alert alert-warning">Invoice not found.</div>
              </div>
            </div>
          <?php endif; ?>
          </section>

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
