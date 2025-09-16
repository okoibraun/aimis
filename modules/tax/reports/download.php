<?php
session_start();
include_once '../../../config/db.php';
include_once '../functions/log_event.php';
include("../../../functions/role_functions.php");

$report_type = $_POST['report_type'];
$from = $_POST['date_from'];
$to = $_POST['date_to'];

$orders;
$total_order_tax = 0;
$total_amount = 0;

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Reports</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php //include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php //include_once("../../../includes/sidebar.phtml"); ?>
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

                <?php if($report_type == "VAT") { ?>
                <?php $orders = $conn->query("SELECT * FROM sales_orders WHERE company_id = $company_id AND order_date >= '$from' AND order_date <= '$to'"); ?>
              
                <section class="content">

                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <?= $report_type ?> Report
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <?php foreach($orders as $order) { ?>
                                        <?php $total_order_tax += $order['tax_amount']; $total_amount += $order['total_amount']; ?>
                                        <?php $items = $conn->query("
                                            SELECT i.*, p.name AS product_name, o.*
                                            FROM sales_order_items i
                                            JOIN sales_products p ON p.id = i.product_id
                                            JOIN sales_orders o ON o.id = i.order_id
                                            WHERE i.order_id = {$order['id']}");
                                        ?>
        
                                        <div class="row">
                                            <div class="col">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Order #</th>
                                                            <td><?= $order['order_number'] ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Order Date</th>
                                                            <td><?= $order['order_date'] ?></td>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="col"></div>
                                        </div>
        
                                        <?php foreach($items as $item) { ?>
                                        <div class="row">
                                            <div class="col">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Items</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Product</th>
                                                                    <th>Quantity</th>
                                                                    <th>Unit Price</th>
                                                                    <th>Total</th>
                                                                    <th>Tax Rate</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($items as $i => $item) { ?>
                                                                <tr>
                                                                    <td><?= $i + 1 ?></td>
                                                                    <td><?= $item['product_name'] ?></td>
                                                                    <td><?= $item['quantity'] ?></td>
                                                                    <td><?= $item['unit_price'] ?></td>
                                                                    <td><?= $item['total'] ?></td>
                                                                    <td><?= $item['tax_rate'] ?></td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
        
                                        <div class="row">
                                            <div class="col"></div>
                                            <div class="col">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Tax Amount</th>
                                                            <td><?= $order['tax_amount'] ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Total Amount</th>
                                                            <td><?= $order['total_amount'] ?></td>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

                <section class="content mt-3 mb-3">
                    <div class="row">
                        <div class="col container">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tfoot>
                                            <tr>
                                                <th style="text-align:right;">Report Total Amount</th>
                                                <td>N<?= number_format($total_amount, 2)?></td>
                                                <th style="text-align:right;">Report Tax Amount</th>
                                                <td>N<?= number_format($total_order_tax, 2) ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php } ?>
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php //include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
