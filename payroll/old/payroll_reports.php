<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch payroll data for reporting
$payroll_result = mysqli_query($conn, "SELECT * FROM payroll ORDER BY payment_date DESC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll</title>
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
            <h1>Payroll Reports</h1>
          </section>

          <section class="content">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Payroll Summary</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Employee</th>
                      <th>Payment Date</th>
                      <th>Gross Salary</th>
                      <th>Tax Deduction</th>
                      <th>Net Salary</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($payroll = mysqli_fetch_assoc($payroll_result)): ?>
                      <tr>
                        <td><?= htmlspecialchars($payroll['employee_name']) ?></td>
                        <td><?= $payroll['payment_date'] ?></td>
                        <td><?= number_format($payroll['gross_salary'], 2) ?></td>
                        <td><?= number_format($payroll['tax_deduction'], 2) ?></td>
                        <td><?= number_format($payroll['net_salary'], 2) ?></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
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
