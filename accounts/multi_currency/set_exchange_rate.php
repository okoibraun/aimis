<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$rate_set = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currency_from = mysqli_real_escape_string($conn, $_POST['currency_from']);
    $currency_to = mysqli_real_escape_string($conn, $_POST['currency_to']);
    $exchange_rate = floatval($_POST['exchange_rate']);
    $date_set = $_POST['rate_date'];

    // Insert the exchange rate into the database
    $stmt = mysqli_prepare($conn, "
        INSERT INTO exchange_rates (company_id, user_id, employee_id, currency_from, currency_to, rate, rate_date)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'iiiiids', $company_id, $user_id, $employee_id, $currency_from, $currency_to, $exchange_rate, $date_set);
    $rate_set = mysqli_stmt_execute($stmt);
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Set Exchange Rate</title>
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
            <h1>Set Exchange Rate</h1>
          </section>

          <section class="content">
          <?php if ($rate_set): ?>
            <div class="alert alert-success">
              <a href="./" class="btn btn-secondary btn-sm float-end">Back</a>
              Exchange rate set successfully.
            </div>
          <?php endif; ?>

          <!-- Set Exchange Rate Form -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Enter Exchange Rate</h3>

            </div>
            <div class="card-body">
              <form method="POST">
                <div class="form-group">
                  <label>From Currency</label>
                  <select name="currency_from" id="" class="form-control">
                    <?php $currencies = $db->query("SELECT * FROM currencies"); foreach($currencies as $currency) { ?>
                    <option value="<?= $currency['id']; ?>"><?= "{$currency['symbol']} - {$currency['name']}"; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="form-group">
                  <label>To Currency</label>
                  <select name="currency_to" id="" class="form-control">
                    <?php $currencies = $db->query("SELECT * FROM currencies"); foreach($currencies as $currency) { ?>
                    <option value="<?= $currency['id']; ?>"><?= "{$currency['symbol']} - {$currency['name']}"; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="form-group">
                  <label>Exchange Rate</label>
                  <input type="number" name="exchange_rate" class="form-control" step="0.01" required>
                </div>

                <div class="form-group">
                  <label>Date</label>
                  <input type="date" name="rate_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <button type="submit" class="btn btn-primary float-end mt-2">Set Exchange Rate</button>
              </form>
            </div>
          </div>

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
