<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


// Querying the number of entries in important tables for quick stats
$accounts_result = mysqli_query($conn, "SELECT COUNT(*) as total_accounts FROM accounts");
$accounts = mysqli_fetch_assoc($accounts_result);

$invoices_result = mysqli_query($conn, "SELECT COUNT(*) as total_invoices FROM invoices");
$invoices = mysqli_fetch_assoc($invoices_result);

$payroll_result = mysqli_query($conn, "SELECT COUNT(*) as total_employees FROM employees");
$payroll = mysqli_fetch_assoc($payroll_result);

$bank_result = mysqli_query($conn, "SELECT COUNT(*) as total_bank_entries FROM bank_reconciliation");
$bank_reconciliation = mysqli_fetch_assoc($bank_result);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts Dashboard</title>
    <?php include_once("../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <section class="content-header">
                <h1>Accounts Dashboard Overview</h1>
            </section>

            <section class="content">
                <div class="row">
                    <!-- Accounts -->
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                        <h3><?= $accounts['total_accounts'] ?></h3>
                        <p>Chart of Accounts</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-book"></i>
                        </div>
                        <a href="accounts/list_accounts.php" class="small-box-footer">View Accounts <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>

                    <!-- Invoices -->
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                        <h3><?= $invoices['total_invoices'] ?></h3>
                        <p>Total Invoices</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-file-invoice"></i>
                        </div>
                        <a href="receivables/list_invoices.php" class="small-box-footer">View Invoices <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>

                    <!-- Payroll -->
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                        <h3><?= $payroll['total_employees'] ?></h3>
                        <p>Employees</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-users"></i>
                        </div>
                        <a href="payroll/list_employees.php" class="small-box-footer">Manage Employees <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>

                    <!-- Bank Reconciliation -->
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                        <h3><?= $bank_reconciliation['total_bank_entries'] ?></h3>
                        <p>Bank Reconciliation Entries</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-money-check-alt"></i>
                        </div>
                        <a href="bank_reconciliation/view_statements.php" class="small-box-footer">View Reconciliation <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Accruals -->
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                        <h3>5</h3> <!-- Placeholder for accruals data -->
                        <p>Payroll Accruals</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                        </div>
                        <a href="payroll/payroll_accruals.php" class="small-box-footer">View Accruals <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>

                    <!-- Financial Reports -->
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                        <h3>10</h3> <!-- Placeholder for report data -->
                        <p>Financial Reports</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                        </div>
                        <a href="accounts/reports.php" class="small-box-footer">View Reports <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>

                    <!-- Journal Entries -->
                    <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                        <h3>20</h3> <!-- Placeholder for journal entries data -->
                        <p>Journal Entries</p>
                        </div>
                        <div class="icon">
                        <i class="fas fa-book-open"></i>
                        </div>
                        <a href="accounts/journal_entries.php" class="small-box-footer">View Entries <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                    </div>
                </div>
            </section>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
