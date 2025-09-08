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
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// $stmt = mysqli_query($conn, "SELECT * FROM exchange_rates ORDER BY date_set DESC");
$exchange_rates = $conn->query("SELECT * FROM exchange_rates WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS Cloud | Accounts - Currencies</title>
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

          <section class="content-header mt-4 mb-4">
            <h3>Multi Currencies</h3>
          </section>

          <section class="content">
            <div class="row">
              <div class="col">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Currencies</h3>
                    <div class="card-tools">
                      <a href="add" class="btn btn-info">Add Currency</a>
                    </div>
                  </div>
                  <div class="card-body">
                    <?php if(in_array($_SESSION['user_role'], system_users())) { ?>
                      <?php $currencies = $conn->query("SELECT * FROM currencies"); ?>
                    <?php } else if(in_array($_SESSION['user_role'], super_roles())) { ?>
                      <?php $currencies = $conn->query("SELECT * FROM currencies WHERE company_id = $company_id"); ?>
                    <?php } else { ?>
                      <?php $currencies = $conn->query("SELECT * FROM currencies WHERE company_id = $company_id AND user_id = $user_id OR employee_id = $employee_id"); ?>
                    <?php } ?>
                    <table class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>Code</th>
                          <th>Name</th>
                          <th>Symbol</th>
                          <th>is Base ?</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($currencies as $currency) { ?>
                          <tr>
                            <td><?= $currency['code']; ?></td>
                            <td><?= $currency['name']; ?></td>
                            <td><?= $currency['symbol']; ?></td>
                            <td><?= $currency['is_base_currency']; ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="col">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Exchange Rates</h3>
                    <div class="card-tools">
                      <a href="set_exchange_rate" class="btn btn-primary">Set Exchange Rate</a>
                    </div>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>From Currency</th>
                          <th>To Currency</th>
                          <th>Exchange Rate</th>
                          <th>Date Set</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($exchange_rates as $row) { ?>
                          <tr>
                            <td><?php $base = $db->query("SELECT symbol FROM currencies WHERE id={$row['currency_from']}")->fetch_assoc(); echo $base['symbol']; ?></td>
                            <td><?php $quote = $db->query("SELECT symbol FROM currencies WHERE id={$row['currency_to']}")->fetch_assoc(); echo $quote['symbol']; ?></td>
                            <td><?= number_format($row['rate'], 4) ?></td>
                            <td><?= $row['rate_date'] ?></td>
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
