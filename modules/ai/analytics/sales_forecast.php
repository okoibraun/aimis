<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

require_once '../functions/openai_api.php'; // we'll create this next

$forecast = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $period = $_POST['period'];
    $region = $_POST['region'];
    $product = $_POST['product'];

    $context = "Sales data for period: $period";
    if (!empty($region)) $context .= ", region: $region";
    if (!empty($product)) $context .= ", product: $product";

    // Simulate historical data injection (stub or real source later)
    $historicalData = "Total sales: 150,000; Growth rate: 12%; Avg ticket size: ₦25,000";
    $prompt = "Based on the following sales context and data, predict future sales trend and key insights:\n\nContext: $context\n\nHistorical Data: $historicalData";

    //$forecast = callOpenAI($prompt);
    $forecast = getSalesForecast([
        'period' => $period,
        'region' => $region,
        'product' => $product,
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
              <section class="content-header">
                <h1><i class="fas fa-chart-line"></i> Sales Forecasting (AI)</h1>
              </section>

              <section class="content">
                <form method="POST" class="card card-info p-3 mb-4">
                  <div class="form-row">
                    <div class="form-group col-md-4">
                      <label>Sales Period</label>
                      <input type="text" name="period" class="form-control" placeholder="e.g. Q3 2025, July 2025" required>
                    </div>
                    <div class="form-group col-md-4">
                      <label>Region (Optional)</label>
                      <input type="text" name="region" class="form-control" placeholder="e.g. Lagos, SW Nigeria">
                    </div>
                    <div class="form-group col-md-4">
                      <label>Product (Optional)</label>
                      <input type="text" name="product" class="form-control" placeholder="e.g. Cement, Paints">
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary">Generate Forecast</button>
                </form>

                <?php if ($forecast): ?>
                  <div class="alert alert-success">
                    <strong>AI Forecast Output:</strong><br>
                    <?= nl2br(htmlspecialchars($forecast)) ?>
                  </div>
                <?php endif; ?>
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
