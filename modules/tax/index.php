<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}


?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax</title>
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

            <div class="content-wrapper">
              <section class="content-header mt-3 mb-5">
                <h1><i class="fas fa-balance-scale"></i> Tax Management Dashboard</h1>
              </section>

              <section class="content">
                <div class="row">

                  <!-- Public Sector -->
                  <div class="col-lg-4 col-md-6">
                    <div class="card bg-gradient-info">
                      <div class="card-body">
                        <h4 class="">Public Sector</h4>
                        <p class="card-text">Fund accounting, grants, IPSAS, budget encumbrance.</p>
                        <a href="funds.php" class="btn btn-outline-primary btn-sm">Manage Funds</a>
                        <a href="grants.php" class="btn btn-outline-primary btn-sm">Grants</a>
                        <a href="budget.php" class="btn btn-outline-primary btn-sm">Encumbrance</a>
                      </div>
                    </div>
                  </div>

                  <!-- Private Sector -->
                  <div class="col-lg-4 col-md-6">
                    <div class="card bg-gradient-success">
                      <div class="card-body">
                        <h4 class="">Private Sector</h4>
                        <p class="card-text">VAT, GST, WHT, tax reports, e-filing support. <br>&nbsp;</p>
                        <a href="setup.php" class="btn btn-outline-primary btn-sm">Configure Taxes</a>
                        <a href="reports.php" class="btn btn-outline-primary btn-sm">Tax Reports</a>
                        <a href="logs.php" class="btn btn-outline-primary btn-sm">Audit Logs</a>
                      </div>
                    </div>
                  </div>

                  <!-- International Compliance -->
                  <div class="col-lg-4 col-md-6">
                    <div class="card bg-gradient-warning">
                      <div class="card-body">
                        <h4 class="">International</h4>
                        <p class="card-text">OECD BEPS, currency compliance, country templates.</p>
                        <a href="rules.php" class="btn btn-outline-primary btn-sm">Tax Rules</a>
                        <a href="updates.php" class="btn btn-outline-primary btn-sm">Update Rates</a>
                        <a href="#" class="btn btn-outline-primary btn-sm">Country Settings</a>
                      </div>
                    </div>
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
