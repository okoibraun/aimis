<?php
session_start();
include_once '../../../config/db.php';
include_once '../functions/log_event.php';
include("../../../functions/role_functions.php");

$report_type = $_POST['report_type'];
$from = $_POST['date_from'];
$to = $_POST['date_to'];

$orders;
$total_tax_amount = 0;
$total_amount = 0;

if($report_type == "VAT") {
    $invoices = $conn->query("
    SELECT si.*, com.name AS company_name
    FROM sales_invoices si
    JOIN companies com ON com.id = si.company_id
    WHERE si.vat_tax_amount != '' AND payment_status = 'paid' AND si.company_id = $company_id AND si.invoice_date >= '$from' AND si.invoice_date <= '$to'");

} else if($report_type == "WHT") {
    $invoices = $conn->query("
    SELECT si.*, com.name AS company_name
    FROM sales_invoices si
    JOIN companies com ON com.id = si.company_id
    WHERE si.wht_tax_amount != '' AND payment_status = 'paid' AND si.company_id = $company_id AND si.invoice_date >= '$from' AND si.invoice_date <= '$to'");

} else {
    $_SESSION['message'] = "Report not available";
    header("Location: ./");
    exit;
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Tax - <?= $report_type ?> Reports</title>
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
            <div class="content-wrapper">
                <section class="content-header mt-4 mb-4">
                    <h1><i class="fas fa-file-alt"></i> Tax - <?= $report_type ?> Report</h1>
                    <p>
                        Generated Report From: <?= $from ?> To: <?= $to ?>
                        <a href="./" class="btn btn-secondary btn-sm float-end">Back</a>
                    </p>
                </section>
              
                <section class="content">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <?= $report_type ?> Report
                                    </h3>
                                    <div class="card-tools tableButtons"></div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped ReportTable">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Invoice Date</th>
                                                <th>Order #</th>
                                                <th>Quotation #</th>
                                                <th>Invoice Tax Amount</th>
                                                <th>Invoice Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($invoices as $invoice) {
                                                if($report_type == "VAT") {
                                                    $total_tax_amount += $invoice['vat_tax_amount'];
                                                } else if($report_type == "WHT") {
                                                    $total_tax_amount += $invoice['wht_tax_amount'];
                                                }

                                                $total_amount += $invoice['total_amount'];
                                            ?>
                                            <tr>
                                                <td><?= $invoice['invoice_number'] ?></td>
                                                <td><?= $invoice['invoice_date'] ?></td>
                                                <td><?= $invoice['order_id'] ? $conn->query("SELECT order_number FROM sales_orders WHERE company_id = {$invoice['company_id']} AND id = {$invoice['order_id']}")->fetch_assoc()['order_number'] : '' ?></td>
                                                <td><?= $invoice['quotation_id'] ? $conn->query("SELECT quote_number FROM sales_quotations WHERE company_id = {$invoice['company_id']} AND id = {$invoice['quotation_id']}")->fetch_assoc()['quote_number'] : '' ?></td>
                                                <td><?= $report_type == "VAT" ? $invoice['vat_tax_amount'] : $invoice['wht_tax_amount'] ?></td>
                                                <td><?= $invoice['total_amount'] ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php
    $total_amount = number_format($total_amount, 2);
    $total_tax_amount = number_format($total_tax_amount, 2);
    ?>
    <script>
        localStorage.setItem('total_tax_amount', "<?= $total_tax_amount ?>");
        localStorage.setItem('total_amount', "<?= $total_amount ?>");
        localStorage.setItem('date_from', "<?= $from ?>");
        localStorage.setItem('date_to', "<?= $to ?>");
    </script>
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
