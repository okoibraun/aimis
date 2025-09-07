<?php
require_once '../../includes/helpers.php'; // Include your helper functions

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$invoice_id = $_GET['id'] ?? 0;
$invoice = $db->query("SELECT i.*, c.name AS customer_name
    FROM sales_invoices i
    JOIN sales_customers c ON c.id = i.customer_id
    WHERE i.id = $invoice_id")->fetch_assoc();

if (!$invoice) {
    exit("Invoice not found. <a href='../../controllers/payments.php'>Go back</a>");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Payments</title>
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
            <section class="content-header">
                <h1>Add Payment – Invoice #<?= $invoice['invoice_number'] ?></h1>
            </section>
            <section class="content">
                <form action="../../controllers/payments.php?action=save" method="post" class="box box-primary p-3">
                    <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>" />
                    
                    <div class="form-group">
                        <label>Customer</label>
                        <p class="form-control-static"><?= htmlspecialchars($invoice['customer_name']) ?></p>
                    </div>

                    <div class="form-group">
                        <label>Invoice Total</label>
                        <p class="form-control-static">$<?= number_format($invoice['total_amount'], 2) ?></p>
                    </div>

                    <div class="form-group">
                        <label>Already Paid</label>
                        <p class="form-control-static">$<?= number_format($invoice['paid_amount'], 2) ?></p>
                    </div>

                    <div class="form-group">
                        <label for="amount_paid">Payment Amount</label>
                        <input type="number" step="0.01" name="amount_paid" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="paid_at">Payment Date</label>
                        <input type="datetime-local" name="paid_at" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" />
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Method</label>
                        <input type="text" name="payment_method" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>

                    <button class="btn btn-success">Save Payment</button>
                    <a href="payments.php" class="btn btn-default">Cancel</a>
                </form>
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
