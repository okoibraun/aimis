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

$filter_month = $_GET['month'] ?? date('Y-m');

$sql = "SELECT e.id, e.first_name, e.last_name, e.department, p.month, p.basic_salary, p.tax_deduction, p.nin_contribution, p.net_salary 
        FROM payslips p
        JOIN employees e ON p.employee_id = e.id
        WHERE e.company_id = $company_id AND p.company_id = e.company_id AND p.month = '$filter_month'
        ORDER BY e.department";

$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll - Report</title>
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

            <div class="content-wrapper">
                <section class="content-header mt-3 mb-3">
                    <h2>Payroll Summary Report - <?= $filter_month ?></h2>
                </section>

                <section class="content">
                    <div class="container-fluid">
                        <form method="get" class="card mb-3">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col">
                                        Filter and Export
                                    </div>
                                    <div class="col"></div>
                                    <div class="col-auto">
                                        <input type="month" name="month" value="<?= $filter_month ?>" class="form-control d-inline-block">
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary">Filter</button>
                                    </div>
                                    <div class="col-auto">
                                        <a href="export_excel.php?month=<?= $filter_month ?>" class="btn btn-success">Export Excel</a>
                                    </div>
                                    <div class="col-auto">
                                        <a href="export_pdf.php?month=<?= $filter_month ?>" class="btn btn-danger">Export PDF</a>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                        
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Summary Report</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered DataTable">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Department</th>
                                            <th>Gross</th>
                                            <th>Tax</th>
                                            <th>Net</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                            <td><?= $row['department'] ?></td>
                                            <td>N<?= $row['basic_salary'] ? number_format($row['basic_salary'], 2) : 0.00; ?></td>
                                            <td>N<?= $row['tax_deduction'] ? number_format($row['tax_deduction'], 2) : 0.00; ?></td>
                                            <td>N<?= $row['net_salary'] ? number_format($row['net_salary'], 2) : 0.00; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot>
                                      <tr>
                                          <?php $total = $conn->query("SELECT SUM(basic_salary) AS total_basic_salary, SUM(net_salary) AS total_net_salary FROM payslips WHERE company_id = $company_id AND month = '$filter_month'")->fetch_assoc(); ?>
                                          <td></td>
                                          <td></td>
                                          <td></td>
                                          <td align="right" class="mt-4">
                                            <p>
                                              <strong>Gross Total: </strong>N<?= $total['total_basic_salary'] ? number_format($total['total_basic_salary'], 2) : 0.00; ?>
                                            </p>
                                            <p>
                                              <strong>Net Total: </strong>N<?= $total['total_net_salary'] ? number_format($total['total_net_salary'], 2) : 0.00; ?>
                                            </p>
                                          </td>
                                          <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
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