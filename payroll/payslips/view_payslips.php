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
    <title>AIMIS | Employee Payslip</title>
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

            <h3>Employee Payslips</h3>

            <form method="GET" class="form-inline mb-3">
                <input type="month" name="month" class="form-control mr-2" value="<?= $_GET['month'] ?? '' ?>">
                <button type="submit" class="btn btn-info">Filter</button>
            </form>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Month</th>
                        <th>Basic</th>
                        <th>Allowances</th>
                        <th>Bonuses</th>
                        <th>Deductions</th>
                        <th>Tax Deduction</th>
                        <th>Social Contribution (NIN)</th>
                        <th>Net</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $filter_month = $_GET['month'] ?? date('Y-m');
                    $res = $conn->query("SELECT p.*, e.first_name, e.last_name 
                                        FROM payslips p 
                                        JOIN employees e ON p.employee_id = e.id 
                                        WHERE p.month = '$filter_month'
                                        ORDER BY e.last_name");
                    while ($row = $res->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                            <td><?= $row['month'] ?></td>
                            <td><?= number_format($row['basic_salary'], 2) ?></td>
                            <td><?= number_format($row['allowances'], 2) ?></td>
                            <td><?= number_format($row['bonuses'], 2) ?></td>
                            <td><?= number_format($row['deductions'], 2) ?></td>
                            <td align="right">-<?= number_format($row['tax_deduction'], 2) ?></td>
                            <td align="right">-<?= number_format($row['nin_contribution'], 2) ?></td>
                            <td><strong><?= number_format($row['net_salary'], 2) ?></strong></td>
                            <td>
                                <a href="payslip_pdf.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">Download PDF</a>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                </tbody>
            </table>

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
