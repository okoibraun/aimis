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

$accrual_scheduled = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accrual_date = $_POST['accrual_date'];
    $amount = floatval($_POST['amount']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $account_id = $_POST['account_id'];

    // Insert the accrual schedule into the database
    $stmt = mysqli_prepare($conn, "
        INSERT INTO accruals (company_id, user_id, employee_id, start_date, amount, description, account_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'iiisdsi', $company_id, $user_id, $employee_id, $accrual_date, $amount, $description, $account_id);
    $accrual_scheduled = mysqli_stmt_execute($stmt);
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Sechedule Accrual</title>
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
                        <a href="./" class="btn btn-primary">List Schedules</a>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <section class="content-header">
                  <h1>Schedule Accrual</h1>
                </section>

                <section class="content">
                <?php if ($accrual_scheduled): ?>
                  <div class="alert alert-success">Accrual scheduled successfully.</div>
                <?php endif; ?>

                <!-- Schedule Accrual Form -->
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Accrual Details</h3>
                  </div>
                  <div class="card-body">
                    <form method="POST">
                      <div class="form-group">
                        <label>Accrual Date</label>
                        <input type="date" name="accrual_date" class="form-control" required>
                      </div>

                      <div class="form-group">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                      </div>

                      <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" required></textarea>
                      </div>

                      <div class="form-group">
                        <label>Account</label>
                        <select name="account_id" class="form-control" required>
                          <?php
                          // Fetch accounts for scheduling
                          $accounts_result = mysqli_query($conn, "SELECT id, account_name FROM accounts ORDER BY account_name ASC");
                          while ($account = mysqli_fetch_assoc($accounts_result)) {
                            echo "<option value='{$account['id']}'>{$account['account_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>

                      <button type="submit" class="btn btn-primary">Schedule Accrual</button>
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