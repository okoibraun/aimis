<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) exit('Invoice ID is required');

$invoice = $db->query("
    SELECT i.*, c.company_name, c.email, c.phone, c.address,
            o.order_number, o.total_amount AS order_total
    FROM sales_invoices i
    LEFT JOIN sales_customers c ON i.customer_id = c.id
    LEFT JOIN sales_orders o ON i.order_id = o.id
    WHERE i.id = $id")->fetch_assoc();

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
            <section class="content-header">
              <h1>Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?? "" ?></h1>
            </section>

            <section class="content">
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Invoice Details</h3>
                  <div class="box-tools">
                    <a href="invoices.php?action=edit&id=<?= $invoice['id'] ?>" class="btn btn-sm btn-warning">
                      <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="invoices.php" class="btn btn-sm btn-default">Back</a>
                  </div>
                </div>
                <div class="box-body">

                  <dl class="dl-horizontal">
                    <dt>Invoice Number</dt>
                    <dd><?= htmlspecialchars($invoice['invoice_number']) ?></dd>

                    <dt>Invoice Date</dt>
                    <dd><?= $invoice['invoice_date'] ?></dd>

                    <dt>Due Date</dt>
                    <dd><?= $invoice['due_date'] ?></dd>

                    <dt>Customer</dt>
                    <dd>
                      <?= htmlspecialchars($invoice['company_name']) ?><br>
                      <?= htmlspecialchars($invoice['email']) ?> | <?= htmlspecialchars($invoice['phone']) ?><br>
                      <?= nl2br(htmlspecialchars($invoice['address'])) ?>
                    </dd>

                    <?php if (!empty($invoice['order_number'])): ?>
                    <dt>Linked Order</dt>
                    <dd>#<?= $invoice['order_number'] ?> (Total: $<?= number_format($invoice['order_total'], 2) ?>)</dd>
                    <?php endif; ?>

                    <dt>Total Amount</dt>
                    <dd><strong>$<?= number_format($invoice['total_amount'], 2) ?></strong></dd>

                    <dt>Amount Received</dt>
                    <dd>$<?= number_format($invoice['payment_received'], 2) ?></dd>

                    <dt>Status</dt>
                    <dd>
                      <span class="label label-<?= match($invoice['status']) {
                        'unpaid' => 'danger',
                        'partial' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'default',
                        default => 'info'
                      } ?>">
                        <?= ucfirst($invoice['status']) ?>
                      </span>
                    </dd>

                    <dt>Notes</dt>
                    <dd><?= nl2br(htmlspecialchars($invoice['notes'])) ?></dd>

                    <dt>Created At</dt>
                    <dd><?= $invoice['created_at'] ?></dd>
                  </dl>

                </div>
              </div>
            </section>

            <section class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col-lg-6">
                        <h4>Payment History</h4>
                      </div>
                      <div class="col-lg-6">
                        <a href="invoice_payments.php?action=add&invoice_id=<?= $invoice['id'] ?>" class="btn btn-sm btn-success">
                          <i class="fa fa-plus"></i> Add Payment
                        </a>
                        <a href="invoices.php?action=pdf&id=<?= $invoice['id'] ?>" class="btn btn-sm btn-default" target="_blank">
                          <i class="fa fa-file-pdf-o"></i> View PDF
                        </a>

                        <a href="invoices.php?action=send_for_signature&id=<?= $invoice['id'] ?>" class="btn btn-warning btn-sm">
                          <i class="fa fa-send"></i> Send for Signature
                        </a>

                        <?php
                        $signature = $db->fetch("SELECT * FROM sales_invoice_signatures WHERE invoice_id = ?", [$invoice['id']]);
                        if ($signature && $signature['status'] === 'signed' && $signature['signed_file_path']):
                        ?>
                          <a href="<?= $signature['signed_file_path'] ?>" class="btn btn-success btn-sm" target="_blank">
                            <i class="fa fa-file-pdf-o"></i> View Signed Invoice
                          </a>
                        <?php endif; ?>

                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Amount</th>
                          <th>Method</th>
                          <th>Reference</th>
                          <th>Notes</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $payments = $db->query("SELECT * FROM sales_invoice_payments WHERE invoice_id = ?", [$invoice['id']]);
                        foreach ($payments as $p):
                        ?>
                          <tr>
                            <td><?= $p['payment_date'] ?></td>
                            <td>$<?= number_format($p['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($p['payment_method']) ?></td>
                            <td><?= htmlspecialchars($p['reference']) ?></td>
                            <td><?= nl2br(htmlspecialchars($p['notes'])) ?></td>
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

            <section class="row">
              <div class="col-lg-6">
                <h4>📎 Attached Documents</h4>
                <ul>
                  <?php
                  $stmt = $conn->prepare("SELECT d.id, d.title, d.file_path FROM document_links dl
                      JOIN documents d ON dl.document_id = d.id
                      WHERE dl.module = 'sales_invoice' AND dl.module_ref_id = ?");
                  $stmt->execute([$invoice_id]);
                  $linkedDocs = $stmt->fetch();

                  foreach ($linkedDocs as $doc) {
                      echo "<li><a href='../../{$doc['file_path']}' target='_blank'>" . htmlspecialchars($doc['title']) . "</a></li>";
                  }
                  ?>
                </ul>

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
