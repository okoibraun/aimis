<?php
require_once '../../../includes/helpers.php';
include("../../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$invoice_id = $_GET['invoice_id'] ?? null;
    if (!$invoice_id) exit('Invoice ID is required');

    $invoice = $db->query("SELECT * FROM sales_invoices WHERE id = $invoice_id AND company_id = $company_id")->fetch_assoc();
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
        $invoice_id, $amount, '$payment_date', '$payment_method', '$reference', '$notes')");

        if($add) {
          // Update payment total and status
          $paid = $db->query("SELECT SUM(amount) AS amount FROM sales_invoice_payments WHERE invoice_id = $invoice_id")->fetch_assoc();
          $status = ($paid['amount'] >= $invoice['total_amount']) ? 'paid' : (($paid['amount'] < $invoice['total_amount']) ? 'partial' : 'unpaid');
        
          // $db->update('sales_invoices', [
          //   'payment_received' => $paid,
          //   'status' => $status
          // ], ['id' => $invoice_id]);

          $db->query("UPDATE sales_invoices SET payment_received={$paid['amount']}, paid_amount={$paid['amount']}, status='$status', payment_status = '$status' WHERE id = $invoice_id");
          
          redirect("/modules/sales/views/invoices/invoice?id=$invoice_id");
        }
    }
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Payments</title>
    <?php include_once("../../../../../includes/head.phtml"); ?>
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
                <section class="content-header mt-3 mb-3">
                    <h1>Add Payment for Invoice #<?= $invoice['invoice_number'] ?></h1>
                </section>

                <section class="content">
                  <div class="row">
                    <div class="col">
                      <form method="post" class="card card-primary">
                          <div class="card-body">
  
                              <div class="form-group">
                                <label>Payment Amount</label>
                                <input type="number" name="amount" class="form-control" step="0.01" required>
                              </div>
  
                              <div class="form-group">
                                <label>Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                              </div>
  
                              <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <select name="payment_method" class="form-control" required>
                                  <?php foreach(['Cash', 'Bank Transfer', 'Card', 'Mobile Money'] as $payment_method) { ?>
                                  <option value="<?= $payment_method; ?>"><?= $payment_method; ?></option>
                                  <?php } ?>
                                </select>
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
  
                          <div class="card-footer">
                            <div class="form-group float-end">
                              <a href="../invoice?id=<?= $invoice['id'] ?>" class="btn btn-default">Cancel</a>
                              <button type="submit" class="btn btn-primary">Record Payment</button>
                            </div>
                          </div>
                      </form>
                    </div>

                    <div class="col">
                      <div class="card">
                        <div class="card-body">
                          <!-- <div class="form-group">
                              <label>Customer</label>
                              <p class="form-control-static"><?= htmlspecialchars($invoice['customer_name']) ?></p>
                          </div> -->

                          <div class="form-group">
                              <label>Invoice Total</label>
                              <p class="form-control-static">N<?= number_format($invoice['total_amount'], 2) ?></p>
                          </div>

                          <div class="form-group">
                              <label>Already Paid</label>
                              <?php if(!empty($invoice['payment_received'])) { ?>
                                <p class="form-control-static">N<?= number_format($invoice['payment_received'], 2) ?></p>
                              <?php } else { ?>
                              <p class="form-control-static">N<?= number_format($invoice['paid_amount'], 2) ?></p>
                              <?php } ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
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
