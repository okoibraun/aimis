<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

$depreciation_scheduled = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_id = $_POST['asset_id'];
    $depreciation_start_date = $_POST['depreciation_start_date'];
    $amount = floatval($_POST['amount']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Insert the depreciation schedule into the database
    $stmt = mysqli_prepare($conn, "
        INSERT INTO depreciation (asset_name, asset_value, start_date, amount)
        VALUES (?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'isds', $asset_id, $depreciation_start_date, $amount, $description);
    $depreciation_scheduled = mysqli_stmt_execute($stmt);
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - List Schedules</title>
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

            <div class="row mt-4 mb-4">
                <div class="col-lg-12">
                    <div class="float-end">
                        <a href="list_schedules.php" class="btn btn-primary">List Schedules</a>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <section class="content-header">
                  <h1>Schedule Depreciation</h1>
                </section>

                <section class="content">
                <?php if ($depreciation_scheduled): ?>
                  <div class="alert alert-success">Depreciation scheduled successfully.</div>
                <?php endif; ?>

                <!-- Schedule Depreciation Form -->
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Depreciation Details</h3>
                  </div>
                  <div class="card-body">
                    <form method="POST">
                      <div class="form-group">
                        <label>Asset</label>
                        <select name="asset_id" class="form-control" required>
                          <?php
                          // Fetch assets for scheduling depreciation
                          $assets_result = mysqli_query($conn, "SELECT id, asset_name FROM assets ORDER BY asset_name ASC");
                          while ($asset = mysqli_fetch_assoc($assets_result)) {
                            echo "<option value='{$asset['id']}'>{$asset['asset_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>

                      <div class="form-group">
                        <label>Depreciation Start Date</label>
                        <input type="date" name="depreciation_start_date" class="form-control" required>
                      </div>

                      <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                      </div>

                      <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" required></textarea>
                      </div>

                      <button type="submit" class="btn btn-primary">Schedule Depreciation</button>
                    </form>
                  </div>
                </div>

                </section>
            </div>
            

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

<?php include '../../includes/footer.php'; ?>