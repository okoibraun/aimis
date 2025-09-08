<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$invoice_id = isset($_GET['invoice_id']) ? intval($_GET['invoice_id']) : 0;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_id = intval($_POST['invoice_id']);
    $payment_date = $_POST['payment_date'];
    $amount = floatval($_POST['amount']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $reference = mysqli_real_escape_string($conn, $_POST['reference']);

    $stmt = $conn->prepare("
        INSERT INTO payments (company_id, user_id, employee_id, invoice_id, payment_date, amount, method, reference)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iiiisdss', $company_id, $user_id, $employee_id, $invoice_id, $payment_date, $amount, $method, $reference);
    $success = $stmt->execute();
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payments</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

          <section class="content-header">
            <h1><?= $invoice_id ? 'Receive Payment' : 'All Payments' ?></h1>
          </section>

          <section class="content">

          <?php if ($success): ?>
            <div class="alert alert-success">Payment recorded successfully.</div>
          <?php endif; ?>

          <?php if ($invoice_id): ?>
            <!-- Payment Form -->
            <div class="card">
              <div class="card-body">
                <form method="POST">
                  <input type="hidden" name="invoice_id" value="<?= $invoice_id ?>">

                  <div class="form-group">
                    <label>Payment Date</label>
                    <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Payment Method</label>
                    <select name="method" class="form-control" required>
                      <option value="Cash">Cash</option>
                      <option value="Bank Transfer">Bank Transfer</option>
                      <option value="Card">Card</option>
                      <option value="Mobile Money">Mobile Money</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Reference (Optional)</label>
                    <input type="text" name="reference" class="form-control">
                  </div>

                  <?php ($success) ? '<a href="./" class="btn btn-danger">Close</a>' : ''; ?>
                  <button type="submit" class="btn btn-primary">Record Payment</button>
                </form>
              </div>
            </div>

          <?php else: ?>
            <!-- Payment List Table -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">All Invoice Payments</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Student</th>
                      <th>Invoice #</th>
                      <th>Amount</th>
                      <th>Method</th>
                      <th>Reference</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if(in_array($_SESSION['user_role'], system_users())) {
  // Get all payments with invoice & customer info
  $payments = $conn->query("
      SELECT p.*, i.invoice_no, c.name
      FROM payments p
      JOIN invoices i ON p.invoice_id = i.id
      JOIN customers c ON i.customer_id = c.id
      ORDER BY p.payment_date DESC
  ");
} else if(in_array($_SESSION['user_role'], super_roles())) {
  // Get all payments with invoice & customer info
  $payments = $conn->query("
      SELECT p.*, i.invoice_no, c.name
      FROM payments p
      JOIN invoices i ON p.invoice_id = i.id
      JOIN customers c ON i.customer_id = c.id
      WHERE p.company_id = $company_id
      ORDER BY p.payment_date DESC
  ");
} else {
  // Get all payments with invoice & customer info
  $payments = $conn->query("
      SELECT p.*, i.invoice_no, c.name
      FROM payments p
      JOIN invoices i ON p.invoice_id = i.id
      JOIN customers c ON i.customer_id = c.id
      WHERE p.company_id = $company_id AND p.user_id = $user_id OR p.employee_id = $employee_id
      ORDER BY p.payment_date DESC
  ");
}
                    ?>
                    <?php foreach ($payments as $p): ?>
                      <tr>
                        <td><?= $p['payment_date'] ?></td>
                        <td><?= htmlspecialchars($p['full_name']) ?></td>
                        <td><?= htmlspecialchars($p['invoice_number']) ?></td>
                        <td><?= number_format($p['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($p['method']) ?></td>
                        <td><?= htmlspecialchars($p['reference']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>

          </section>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
