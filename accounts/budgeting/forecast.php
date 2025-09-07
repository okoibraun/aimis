<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


// Fetch budgets for forecasting
$budgets_result = mysqli_query($conn, "SELECT * FROM budgets ORDER BY start_date DESC");

$forecast_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $budget_id = $_POST['budget_id'];
    
    // Example logic for forecast: calculate forecasted amounts based on current budget
    $result = mysqli_query($conn, "SELECT * FROM budgets WHERE id = $budget_id");
    $budget = mysqli_fetch_assoc($result);

    $forecast_data['budget_name'] = $budget['budget_name'];
    $forecast_data['forecasted_amount'] = $budget['total_amount'] * 1.1;  // A simple 10% increase forecast for illustration
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Budget Forecast</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

          <section class="content-header">
            <h1>Budget Forecast</h1>
          </section>

          <section class="content">
            <form method="POST">
              <div class="form-group">
                <label>Budget</label>
                <select name="budget_id" class="form-control" required>
                  <?php
                  while ($budget = mysqli_fetch_assoc($budgets_result)) {
                    echo "<option value='{$budget['id']}'>{$budget['budget_name']}</option>";
                  }
                  ?>
                </select>
              </div>

              <button type="submit" class="btn btn-primary">Generate Forecast</button>
            </form>

            <?php if ($forecast_data): ?>
              <div class="card mt-3">
                <div class="card-header">
                  <h3 class="card-title"><?= $forecast_data['budget_name'] ?> Forecast</h3>
                </div>
                <div class="card-body">
                  <p><strong>Forecasted Amount:</strong> $<?= number_format($forecast_data['forecasted_amount'], 2) ?></p>
                </div>
              </div>
            <?php endif; ?>
          </section>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
