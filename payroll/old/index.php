<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll Dashboard</title>
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
                <h1>Payroll</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                        <h3>0</h3>
                        <p>Employees</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-users"></i>
                        </div>
                        <a href="/accounts/payroll/list_employees.php" class="small-box-footer">View Employees <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>
                    
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                        <h3>0</h3>
                        <p>Generated Pay Slips</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-file-invoice"></i>
                        </div>
                        <a href="/accounts/payroll/generate_pay_slips.php" class="small-box-footer">Generate Pay Slips <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>

                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                        <h3>0</h3>
                        <p>Payroll Reports</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="/accounts/payroll/payroll_reports.php" class="small-box-footer">View Reports <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>

                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                        <h3>0</h3>
                        <p>Accrued Salaries</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                        </div>
                        <a href="/accounts/payroll/payroll_accruals.php" class="small-box-footer">View Accruals <i class="fas fa-arrow-circle-right"></i></a>
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
