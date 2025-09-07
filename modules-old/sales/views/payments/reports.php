<?php
require_once '../../includes/helpers.php'; // Include your helper functions

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$invoices = $db->query("
    SELECT i.*, c.name AS customer_name,
        DATEDIFF(NOW(), i.invoice_date) AS days_outstanding
    FROM sales_invoices i
    JOIN sales_customers c ON c.id = i.customer_id
    WHERE i.payment_status != 'paid'
    ORDER BY i.invoice_date ASC
");
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
                <div class="row">
                  <div class="col-lg-6">
                    <h2>Payment Aging Report</h2>
                    <!-- <p class="text-muted">View outstanding invoices and their aging status</p> -->
                  </div>
                  <div class="col-lg-6 float-end">
                    <ol class="breadcrumb float-end">
                        <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Sales</a></li>
                        <li class="breadcrumb-item active">Payments</li>
                    </ol>
                  </div>
                </div>
            </section>
            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Outstanding Invoices</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Invoice Date</th>
                                    <th>Days Outstanding</th>
                                    <th>Aging Bucket</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $inv): 
                                    $balance = $inv['total_amount'] - $inv['paid_amount'];
                                    $days = $inv['days_outstanding'];
                                    if ($days <= 30) $bucket = '0–30';
                                    elseif ($days <= 60) $bucket = '31–60';
                                    elseif ($days <= 90) $bucket = '61–90';
                                    else $bucket = '90+';
                                ?>
                                <tr>
                                    <td><?= $inv['invoice_number'] ?></td>
                                    <td><?= htmlspecialchars($inv['customer_name']) ?></td>
                                    <td>$<?= number_format($inv['total_amount'], 2) ?></td>
                                    <td>$<?= number_format($inv['paid_amount'], 2) ?></td>
                                    <td>$<?= number_format($balance, 2) ?></td>
                                    <td><span class="label label-<?= $inv['payment_status'] === 'paid' ? 'success' : ($inv['payment_status'] === 'partial' ? 'warning' : 'danger') ?>">
                                    <?= strtoupper($inv['payment_status']) ?>
                                    </span></td>
                                    <td><?= $inv['invoice_date'] ?></td>
                                    <td><?= $days ?> days</td>
                                    <td><?= $bucket ?></td>
                                    <td>
                                        <a href="../../controllers/payments.php?action=add&id=<?= $inv['id'] ?>" class="btn btn-xs btn-success">Add Payment</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
