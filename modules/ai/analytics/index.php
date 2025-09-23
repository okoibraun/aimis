<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

require_once '../functions/openai_api.php'; // we'll create this next

$forecast = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $period = $_POST['period'];
    // $region = $_POST['region'];
    $product = $_POST['product'];
    $historyData = $_POST['historical_data'];

    $product_name = "";
    $historicalData = "";

    $context = "Sales data for period: $period";
    // if (!empty($region)) $context .= ", region: $region";
    // if (!empty($product)) $context .= ", product: $product";

    // Simulate historical data injection (stub or real source later)
    if(!empty($historyData)) {
      //$historicalData = "Total sales: 150,000; Growth rate: 12%; Avg ticket size: ₦25,000";
      $historicalData = $historyData;
    } else {
      //get product
      $get_product = $conn->query("SELECT * FROM sales_products WHERE id = $product AND company_id = $company_id")->fetch_assoc();
      // Set product to get product name from db
      $context .= ", product: {$get_product['name']}";
      $product_name = $get_product['name'];
      //get order items where product exist
      $get_order_items = $conn->query("SELECT SUM(quantity) AS total_qty, SUM(total) AS total_amount FROM sales_order_items WHERE product_id = '{$get_product['id']}'")->fetch_assoc();

      // $historicalData = "Total sales: {$get_order_items['total_qty']}; Avg product size: {$get_order_items['total_products']}"
      $total_amount = number_format($get_order_items['total_amount'], 2);
      $average_sales = $get_order_items['total_amount'] / $get_order_items['total_qty'];
      $avg = number_format($average_sales, 2);
      //$historicalData = "Total sales: {$get_order_items['total_qty']}; Avg product size: ₦{$avg}; date: {$period}; Amount: ₦{$total_amount}";
      $historicalData = "Total sales: {$get_order_items['total_qty']}; Date: {$period}; Amount: ₦{$total_amount}";
    }

    $prompt = "Based on the following sales context and data, predict future sales trend and key insights:\n\nContext: $context\n\nHistorical Data: $historicalData";

    //$forecast = callOpenAI($prompt);
    $forecast = getSalesForecast([
        'period' => $period,
        //'region' => $region,
        'product' => $product_name,
        'historical' => $historicalData
    ]);

}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | AI - Analytics</title>
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
              <section class="content-header mt-3 mb-3">
                <h1>
                    <i class="fas fa-chart-line"></i> Sales Forecasting (AI)
                </h1>
                <p>Use AI to analyze &amp; predict future sales based on historical data.</p>
              </section>

              <section class="content">
                <div class="row">
                    <div class="col-md-4">
                      <form method="POST" class="card p-1">
                        <div class="card-header">
                          <div class="card-tools">
                              <a href="../" class="btn btn-sm btn-danger">X</a>
                          </div>
                        </div>
                        <div class="card-body">
                          <h2>How it works</h2>
                          <!-- <p>Select Period and select a product to get the forecast.</p> -->
                          <p>This module uses AI to analyze historical sales data and predict future sales trends.</p>
                          <p>It can help you make informed decisions about marketing, and sales strategies.</p>
                          <p>Simply select the period and select a product to get started.</p>

                          <div class="form-group mt-3">
                              <label>Sales Period</label>
                              <!-- <input type="text" name="period" class="form-control" placeholder="e.g. Q3 2025, July 2025" required> -->
                              <input type="month" name="period" class="form-control" required>
                          </div>
                          <!-- <div class="form-group mt-3 col">
                              <label>Region (Optional)</label>
                              <input type="text" name="region" class="form-control" placeholder="e.g. Lagos, SW Nigeria">
                          </div> -->
                          <div class="form-group mt-3">
                              <label>Historial Data (Optional)</label>
                              <input type="text" name="historical_data" class="form-control" placeholder="e.g. Total sales: 150,000; Growth rate: 12%; Avg ticket size: ₦25,000">
                          </div>
                          <div class="form-group mt-3 mb-3">
                              <label>Product</label>
                              <select name="product" class="form-control" required>
                                  <option value="">-- Select Product --</option>
                                  <?php $res = $conn->query("SELECT id, name FROM sales_products WHERE company_id = $company_id");
                                    foreach($res as $r) { ?>
                                      <option value='<?= $r['id'] ?>'><?= $r['name'] ?></option>
                                  <?php } ?>
                              </select>
                          </div>
                        </div>
                          <button type="submit" class="btn btn-primary mb-2">Forecast</button>
                      </form>
                    </div>
                    <div class="col-md-8">
                      <?php if ($forecast): ?>
                        <div class="alert alert-success">
                          <strong>AIMIS AI Forecast Result:</strong><br>
                          <?= nl2br(htmlspecialchars($forecast)) ?>
                        </div>
                      <?php endif; ?>
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
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
