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

// Handle POST request
$forecast_result = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from = $_POST['from'];
    $to = $_POST['to'];
    $product_id = $_POST['product_id'];

    // Fetch sales history from DB
    $sales_data = [];
    $sql = "SELECT invoice_date, SUM(quantity) as total_qty 
            FROM sales_invoices si
            JOIN sales_invoice_items sii ON si.id = sii.invoice_id
            WHERE sii.product_id = $product_id AND invoice_date BETWEEN '$from' AND '$to'
            GROUP BY invoice_date";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $sales_data[] = ['date' => $row['invoice_date'], 'qty' => $row['total_qty']];
    }

    // Prepare input for AI
    $forecast = getSalesForecast([
        'period' => $period,
        'region' => $region,
        'product' => $product,
        'historical' => $historicalData
    ]);

    $input = json_encode($sales_data);

    // Call OpenAI or other AI model
    $ai_response = getSalesForecast($input); // from openai_api.php

    // Save to ai_predictions
    $stmt = $conn->prepare("INSERT INTO ai_predictions (prediction_type, reference_id, prediction_result, prediction_score, predicted_for_period) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $type, $ref_id, $result, $score, $period);
    $type = 'sales_forecast';
    $ref_id = $product_id;
    $result = $ai_response['forecast'];
    $score = $ai_response['confidence'];
    $period = "$from to $to";
    $stmt->execute();

    // Also log
    mysqli_query($conn, "INSERT INTO ai_logs (module, feature, input_data, output_data, confidence_score, created_by)
                         VALUES ('sales', 'forecast', '".mysqli_real_escape_string($conn, $input)."',
                         '".mysqli_real_escape_string($conn, $result)."', '$score', 1)");

    $forecast_result = $result;
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - BOM</title>
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
                    <h1><i class="fas fa-chart-line"></i> AI Sales Forecasting</h1>
                </section>

                <section class="content">
                    <div class="row">
                        <div class="col-md-6">
                            <h2>Sales Forecasting</h2>
                            <p>Use AI to predict future sales based on historical data.</p>
                            <p>Enter the date range and select a product to get the forecast.</p>

                            <form method="POST" class="card card-primary p-3">
                                <div class="row mb-3">
                                    <div class="form-group col-md-4">
                                        <label>From</label>
                                        <input type="date" name="from" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>To</label>
                                        <input type="date" name="to" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Product</label>
                                        <select name="product_id" class="form-control" required>
                                            <option value="">-- Select Product --</option>
                                            <?php
                                            $res = mysqli_query($conn, "SELECT id, name FROM sales_products");
                                            foreach($res as $r) {
                                                echo "<option value='{$r['id']}'>{$r['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Forecast</button>
                            </form>
                        </div>
                        <div class="col-md-6 mt-5">
                            <div class="mt-5">&nbsp;</div>
                            <h2>How it works</h2>
                            <p>This module uses AI to analyze historical sales data and predict future sales trends.</p>
                            <p>It can help you make informed decisions about inventory, marketing, and sales strategies.</p>
                            <p>Simply enter the date range and select a product to get started.</p>
                        </div>
                    </div>

                    <?php if ($forecast_result): ?>
                    <div class="alert alert-success mt-4">
                        <h5>Forecast Result:</h5>
                        <pre><?= htmlspecialchars($forecast_result) ?></pre>
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
