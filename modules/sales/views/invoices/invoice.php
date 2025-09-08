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

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) exit('Invoice ID is required');

$invoice = $db->query("
    SELECT i.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address,
            l.name AS lead_name, l.email AS lead_email, l.phone AS lead_phone, l.address AS lead_address,
            o.order_number, o.total_amount AS order_total, q.quote_number, q.total AS quote_total, q.tax AS quote_tax
    FROM sales_invoices i
    LEFT JOIN sales_quotations q ON i.quotation_id = q.id
    LEFT JOIN sales_customers c ON i.customer_id = c.id
    LEFT JOIN sales_customers l ON i.lead_id = l.id
    LEFT JOIN sales_orders o ON i.order_id = o.id
    WHERE i.id = $id AND i.company_id = $company_id")->fetch_assoc();

if (!$invoice) exit("Invoice not found.");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Invoices</title>
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
              <h1>Invoice #<?= $invoice['invoice_number'] ?? "" ?></h1>
            </section>

            <section class="content">
              <div class="card card-solid">
                <div class="card-header with-border">
                  <h3 class="card-title">Invoice Details</h3>
                  <div class="card-tools">
                    <a href="./" class="btn btn-sm btn-secondary">Back</a>
                    <a href="edit.php?id=<?= $invoice['id'] ?>" class="btn btn-sm btn-primary">
                      <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="invoices?action=pdf&id=<?= $invoice['id'] ?>" class="btn btn-sm btn-info" target="_blank">
                      <i class="fa fa-file-pdf-o"></i> View PDF
                    </a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <dl class="dl-horizontal">
                        <dt>Invoice Number</dt>
                        <dd><?= $invoice['invoice_number'] ?></dd>
                      </dl>
                    </div>

                    <div class="col">
                      <dl class="dl-horizontal">
                        <dt>Invoice Date</dt>
                        <dd><?= $invoice['invoice_date'] ?></dd>

                        <dt>Due Date</dt>
                        <dd><?= $invoice['due_date'] ?></dd>

                        <dt>Status</dt>
                        <dd>
                          <span class="">
                            <?= ucfirst($invoice['status']) ?>
                          </span>
                        </dd>
                      </dl>
                    </div>

                    <div class="col">
                      <dl class="dl-horizontal">
    
                        <?php if (!empty($invoice['order_number'])): ?>
                        <dt>Linked Order</dt>
                        <dd>#<?= $invoice['order_number'] ?> (Total: N<?= number_format($invoice['order_total'], 2) ?>)</dd>
                        <?php endif; ?>

                        <?php if (!empty($invoice['quote_number'])): ?>
                        <dt>Linked Quotation</dt>
                        <dd>#<?= $invoice['quote_number'] ?> (Total: N<?= number_format($invoice['quote_total'], 2) ?>)</dd>
                        <?php endif; ?>
    
                        <dt>Total Amount</dt>
                        <dd><strong>N<?= number_format($invoice['total_amount'], 2) ?></strong></dd>
    
                        <dt>Amount Received</dt>
                        <dd>N<?= number_format($invoice['payment_received'], 2) ?></dd>

                        <dt>Balance</dt>
                        <?php $balance_neg = $invoice['total_amount'] - $invoice['payment_received']; $balance = $invoice['payment_received'] - $invoice['total_amount']; ?> 
                        <dd>N<?= number_format($balance_neg, 2) ?> (N<?= number_format($balance, 2) ?>)</dd>
                      </dl>
                    </div>
                    <div class="col">
                      <dl class="dl-horizontal">
                        <dt>Created At</dt>
                        <dd><?= $invoice['created_at'] ?></dd>
                      </dl>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col">
                      <dl class="dl-horizontal">

                        <?php if(!empty($invoice['customer_name'])) { ?>
                        <dt>Customer</dt>
                        <dd>
                          <?= $invoice['customer_name'] ?><br>
                          <?= $invoice['customer_email'] ?> | <?= $invoice['customer_phone'] ?><br>
                          <?= nl2br($invoice['customer_address']) ?>
                        </dd>
                        <?php } ?>
                        <?php if(!empty($invoice['lead_name'])) { ?>
                        <dt>Customer (Lead)</dt>
                        <dd>
                          <?= $invoice['lead_name'] ?><br>
                          <?= $invoice['lead_email'] ?> | <?= $invoice['lead_phone'] ?><br>
                          <?= nl2br($invoice['lead_address']) ?>
                        </dd>
                        <?php } ?>
                      </dl>
                    </div>
                    <div class="col"></div>
                    <div class="col"></div>
                  </div>

                  <div class="row">
                    <div class="col">
                      <dl class="dl-horizontal">
    
                        <dt>Notes</dt>
                        <dd><?= nl2br($invoice['notes']) ?></dd>
                      </dl>
                    </div>
                    <div class="col">
                    </div>
                  </div>

                </div>
              </div>
            </section>

            <section class="row mt-4">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Payment History</h3>
                    <div class="card-tools">
                      <a href="./payments/add.php?action=add&invoice_id=<?= $invoice['id'] ?>" class="btn btn-sm btn-success">
                        <i class="fa fa-plus"></i> Add Payment
                      </a>

                      <!-- <a href="invoices?action=send_for_signature&id=<?= $invoice['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-send"></i> Send for Signature
                      </a> -->

                      <?php
                      $signature = $db->query("SELECT * FROM sales_invoice_signatures WHERE invoice_id = {$invoice['id']}")->fetch_assoc();
                      if ($signature && $signature['status'] === 'signed' && $signature['signed_file_path']):
                      ?>
                        <a href="<?= $signature['signed_file_path'] ?>" class="btn btn-success btn-sm" target="_blank">
                          <i class="fa fa-file-pdf-o"></i> View Signed Invoice
                        </a>
                      <?php endif; ?>

                    </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Amount (N)</th>
                          <th>Method</th>
                          <th>Reference</th>
                          <th>Notes</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $payments = $db->query("SELECT * FROM sales_invoice_payments WHERE invoice_id = {$invoice['id']}");
                        foreach ($payments as $p):
                        ?>
                          <tr>
                            <td><?= $p['payment_date'] ?></td>
                            <td>N<?= number_format($p['amount'], 2) ?></td>
                            <td><?= $p['payment_method'] ?></td>
                            <td><?= $p['reference'] ?></td>
                            <td><?= nl2br($p['notes']) ?></td>
                          </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                          <tr><td colspan="5">No payments yet.</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>

                    

                  </div>
                </div>
              </div>
            </section>

            <section class="row mt-4" style="display:none">
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      📎 Attached Documents
                    </h3>
                  </div>
                  <div class="card-body">
                    <ul>
                      <?php
                      $stmt = $conn->query("SELECT d.id, d.title, d.file_path FROM document_links dl
                          JOIN documents d ON dl.document_id = d.id
                          WHERE dl.module = 'sales_invoice' AND dl.module_ref_id = {$invoice['id']}");
                      $linkedDocs = $stmt->fetch_assoc();
    
                      foreach ($stmt as $doc) {
                          echo "<li><a href='../../{$doc['file_path']}' target='_blank'>" . $doc['title'] . "</a></li>";
                      }
                      ?>
                    </ul>
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
