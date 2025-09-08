<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

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

$bill_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bill_id === 0) {
    echo "<div class='alert alert-danger'>Invalid Bill ID</div>";
    exit;
}

// Fetch bill details
$bill = $conn->query("SELECT b.*, v.name AS vendor_name FROM bills b JOIN accounts_vendors v ON b.vendor_id = v.id WHERE b.id = $bill_id AND b.company_id = $company_id LIMIT 1")->fetch_assoc();
if (!$bill) {
    echo "<div class='alert alert-danger'>Bill not found.</div>";
    exit;
}

// Fetch payment history for the bill
$payments_result = mysqli_query($conn, "SELECT * FROM payments WHERE bill_id = $bill_id ORDER BY payment_date DESC");

// Handle payment submission
$payment_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_date = $_POST['payment_date'];
    $amount = floatval($_POST['amount']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $reference = mysqli_real_escape_string($conn, $_POST['reference']);

    // Insert payment into the database
    $stmt = $conn->prepare("INSERT INTO payments (company_id, user_id, employee_id, bill_id, payment_date, amount, method, reference) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiisdss', $company_id, $user_id, $employee_id, $bill_id, $payment_date, $amount, $method, $reference);
    $payment_success = $stmt->execute();

    // Update bill's paid amount
    if ($payment_success) {
        $new_paid_amount = $bill['paid_amount'] + $amount;
        $update_bill_stmt = $conn->prepare("UPDATE bills SET paid_amount = ? WHERE id = ?");
        $update_bill_stmt->bind_param('di', $new_paid_amount, $bill_id);
        $update_bill_stmt->execute();

        header("Location: bill?id={$bill_id}");
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | View Bill</title>
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

          <section class="content-header mt-3 mb-3">
            <div class="row">
              <div class="col">
                <h1>View Bill</h1>
              </div>
              <div class="col-auto">
                <a href="./" class="btn btn-secondary">All Bills</a>
              </div>
            </div>
          </section>

          <section class="content">
            <?php if ($payment_success): ?>
            <div class="alert alert-success">Payment recorded successfully.</div>
            <?php endif; ?>

            <div class="row">
              <div class="col-lg-6">
                <!-- Bill Information -->
                <div class="card">
                  <div class="card-body">
                    <p><strong>Vendor:</strong> <?= htmlspecialchars($bill['vendor_name']) ?></p>
                    <p><strong>Bill Date:</strong> <?= $bill['bill_date'] ?></p>
                    <p><strong>Due Date:</strong> <?= $bill['due_date'] ?></p>
                    <p><strong>Total Amount:</strong> <?= number_format($bill['amount'], 2) ?></p>
                    <p><strong>Paid Amount:</strong> <?= $bill['paid_amount'] ?></p>
                    <p><strong>Remaining Balance:</strong> <?= number_format($bill['amount'] - $bill['paid_amount'], 2) ?></p>
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <!-- Payment Form -->
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Record a Payment</h3>
                  </div>
                  <div class="card-body">
                    <form method="POST">
                      <div class="form-group">
                        <label>Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
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
      
                      <button type="submit" class="btn btn-primary">Record Payment</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-lg-12">
                <!-- Payment History Table -->
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Payment History</h3>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover DataTable">
                      <thead>
                        <tr>
                          <th>Payment Date</th>
                          <th>Amount</th>
                          <th>Method</th>
                          <th>Reference</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while ($payment = mysqli_fetch_assoc($payments_result)): ?>
                          <tr>
                            <td><?= $payment['payment_date'] ?></td>
                            <td><?= number_format($payment['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($payment['method']) ?></td>
                            <td><?= htmlspecialchars($payment['reference']) ?></td>
                          </tr>
                        <?php endwhile; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
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
