<?php
require_once '../../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../../login.php');
    exit();
}

$invoice_id = $_GET['invoice_id'] ?? null;
    if (!$invoice_id) exit('Invoice ID is required');

    $invoice = $db->query("SELECT * FROM sales_invoices WHERE id = $invoice_id")->fetch_assoc();
    if (!$invoice) exit('Invoice not found');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $amount = $_POST['amount'];
        $payment_date = $_POST['payment_date'];
        $payment_method = $_POST['payment_method'];
        $reference = $_POST['reference'];
        $notes = $_POST['notes'];

        // $db->insert('sales_invoice_payments', [
        //     'invoice_id' => $invoice_id,
        //     'amount' => $amount,
        //     'payment_date' => $payment_date,
        //     'payment_method' => $payment_method,
        //     'reference' => $reference,
        //     'notes' => $notes
        // ]);

        $add = $db->query("INSERT INTO sales_invoice_payments (invoice_id, amount, payment_date, payment_method, reference, notes) VALUES (
        $invoice_id, $amount, $payment_date, $payment_method, $reference, $notes)");

        if($add) {
          // Update payment total and status
          $paid = $db->query("SELECT SUM(amount) FROM sales_invoice_payments WHERE invoice_id = $invoice_id")->fetch_assoc();
          $status = ($paid >= $invoice['total_amount']) ? 'paid' : (($paid > 0) ? 'partial' : 'unpaid');
        
          // $db->update('sales_invoices', [
          //   'payment_received' => $paid,
          //   'status' => $status
          // ], ['id' => $invoice_id]);

          $db->query("UPDATE sales_invoices SET payment_received=$paid, status=$status WHERE id = $invoice_id");
          
          redirect("../invoices.php?action=details&id=$invoice_id");
        }
    }
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Payments</title>
    <?php include_once("../../../../.../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
          

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>Add Payment for Invoice #<?= $invoice['invoice_number'] ?></h1>
                </section>

                <section class="content">
                    <form method="post" class="box box-primary">
                        <div class="box-body">

                            <div class="form-group">
                            <label>Payment Amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                            </div>

                            <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="form-group">
                            <label>Payment Method</label>
                            <input type="text" name="payment_method" class="form-control" placeholder="e.g. Bank Transfer, Credit Card">
                            </div>

                            <div class="form-group">
                            <label>Reference</label>
                            <input type="text" name="reference" class="form-control" placeholder="e.g. Transaction ID">
                            </div>

                            <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                            </div>

                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Record Payment</button>
                            <a href="invoices.php?action=details&id=<?= $invoice['id'] ?>" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
                </section>
            </div>
          

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
