<?php
require_once '../../includes/helpers.php'; // Include your helper functions
include("../../../../functions/role_functions.php");

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

$invoice_id = $_GET['id'] ?? 0;
// $invoice = $db->query("SELECT i.*, c.name AS customer_name, l.name AS lead_name, q.quote_number, o.order_number
//     FROM sales_invoices i
//     JOIN sales_customers c ON c.id = i.customer_id
//     JOIN sales_customers l ON l.id = i.lead_id
//     JOIN sales_quotations q ON q.id = i.quotation_id
//     JOIN sales_orders o ON o.id = i.order_id
//     WHERE i.id = $invoice_id")->fetch_assoc();
$invoice = $conn->query("SELECT * FROM sales_invoices WHERE id = $invoice_id AND company_id = $company_id")->fetch_assoc();

if (!$invoice) {
    exit("Invoice not found. <a href='./'>Go back</a>");
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
            <section class="content-header mt-3 mb-3">
                <h1>Add Payment – Invoice #<?= $invoice['invoice_number'] ?></h1>
            </section>
            <section class="content">
              <div class="row">
                <div class="col">
                  <form action="../../controllers/payments.php?action=save" method="post" class="box box-primary p-3">
                    <div class="card">
                      <div class="card-body">
                        <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>" />
                        
                        <!-- <?php if(!empty($invoice['customer_name'])) { ?>
                        <div class="form-group">
                            <label>Customer</label>
                            <p class="form-control-static"><?= $invoice['customer_name'] ?></p>
                        </div>
                        <?php } ?>
    
                        <div class="form-group">
                            <label>Invoice Total</label>
                            <p class="form-control-static">N<?= number_format($invoice['total_amount'], 2) ?></p>
                        </div>
    
                        <div class="form-group">
                            <label>Already Paid</label>
                            <p class="form-control-static">N<?= number_format($invoice['paid_amount'], 2) ?></p>
                        </div> -->
    
                        <div class="form-group">
                            <label for="amount_paid">Payment Amount</label>
                            <input type="number" step="0.01" name="amount_paid" class="form-control" required />
                        </div>
    
                        <div class="form-group">
                            <label for="paid_at">Payment Date</label>
                            <input type="datetime-local" name="paid_at" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" />
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
                            <label for="notes">Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>

                      </div>
                    </div>
  
                      <button class="btn btn-success">Save Payment</button>
                      <a href="./" class="btn btn-default">Cancel</a>
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
