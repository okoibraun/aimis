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
                                    <div class="card-tools tableButtons">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table ReportTable">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Order Date</th>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th>Item Unit Price</th>
                                                <th>Item Total</th>
                                                <th>Item Tax Rate</th>
                                                <th>Order Tax Amount</th>
                                                <th>Order Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($orders as $order) { ?>
                                                <?php $total_order_tax += $order['tax_amount']; $total_amount += $order['total_amount']; ?>
                                                <?php $items = $conn->query("
                                                    SELECT i.*, p.name AS product_name, o.*
                                                    FROM sales_order_items i
                                                    JOIN sales_products p ON p.id = i.product_id
                                                    JOIN sales_orders o ON o.id = i.order_id
                                                    WHERE i.order_id = {$order['id']}");

                                                    $processed = []; // Array to track processed items
                                                ?>
                                                
                                                <?php foreach($items as $item) { ?>
                                                <tr>
                                                    <td>
                                                        <?= $item['order_number']; ?>
                                                    </td>
                                                    <td><?= $item['order_date'] ?></td>
                                                    <td><?= $item['product_name'] ?></td>
                                                    <td><?= $item['quantity'] ?></td>
                                                    <td><?= $item['unit_price'] ?></td>
                                                    <td><?= $item['total'] ?></td>
                                                    <td><?= $item['tax_rate'] ?></td>
                                                    <td>
                                                        <?php
                                                            if(!in_array($item['tax_amount'], $processed)) {
                                                                $processed[] = $item['tax_amount'];
                                                                echo $item['tax_amount'];
                                                            } else {
                                                                echo "";
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            if(!in_array($item['total_amount'], $processed)) {
                                                                echo $item['total_amount'];
                                                                $processed[] = $item['total_amount'];
                                                            } else {
                                                                echo "";
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                
                                                <?php } ?>
                                                <tr class="mt-4 mb-2">
                                                    <th>Report Total Amount</th>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>N<?= number_format($total_amount, 2)?></td>
                                                </tr>
                                                <tr>
                                                    <th>Report Tax Amount</th>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>N<?= number_format($total_order_tax, 2) ?></td>
                                                    <td></td>
                                                </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

                <!-- <section class="content mt-3 mb-3">
                    <div class="row">
                        <div class="col container">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tfoot>
                                            <tr>
                                                <th style="text-align:right;"></th>
                                                <td></td>
                                                <th style="text-align:right;">Report Tax Amount</th>
                                                <td>N<?= number_format($total_order_tax, 2) ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section> -->
                <?php } ?>
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
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
