<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['employee_id'])) {
  echo "<div class='alert alert-danger'>No employee ID provided.</div>";
  exit;
}

$employee_id = intval($_GET['employee_id']);

// Get employee name
$emp_sql = "SELECT first_name, last_name FROM employees WHERE id = $employee_id";
$emp_result = $conn->query($emp_sql);
if ($emp_result->num_rows === 0) {
  echo "<div class='alert alert-warning'>Employee not found.</div>";
  exit;
}
$emp = $emp_result->fetch_assoc();

// Get payslips
$sql = "SELECT * FROM payroll WHERE employee_id = $employee_id ORDER BY month DESC";
$result = $conn->query($sql);
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

            <div class="content-wrapper">
                <section class="content-header mb-4">
                    <h1>Payslip History - <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></h1>
                    <a href="../employees/view_employee.php?id=<?= $employee_id; ?>" class="btn btn-primary"><- Back</a>
                </section>

                <section class="content">
                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">All Payslips</h3>
                    </div>
                    <div class="card-body table-responsive">
                    <?php if ($result->num_rows > 0): ?>
                        <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                            <th>Month</th>
                            <th>Basic Salary</th>
                            <th>Bonus</th>
                            <th>Overtime</th>
                            <th>Deductions</th>
                            <th>Tax</th>
                            <th>Social Security</th>
                            <th>Gross</th>
                            <th>Net</th>
                            <th>Status</th>
                            <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['month'] ?></td>
                                <td>$<?= number_format($row['basic_salary'], 2) ?></td>
                                <td>$<?= number_format($row['bonus'], 2) ?></td>
                                <td>$<?= number_format($row['overtime'], 2) ?></td>
                                <td>$<?= number_format($row['deductions'], 2) ?></td>
                                <td>$<?= number_format($row['tax'], 2) ?></td>
                                <td>$<?= number_format($row['social_security'], 2) ?></td>
                                <td>$<?= number_format($row['gross_salary'], 2) ?></td>
                                <td><b>$<?= number_format($row['net_salary'], 2) ?></b></td>
                                <td>
                                <?= $row['paid_status'] ? "<span class='badge badge-success'>Paid</span>" : "<span class='badge badge-secondary'>Unpaid</span>" ?>
                                </td>
                                <td>
                                <a href="generate_payslip_pdf.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">PDF</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">No payslips found for this employee.</div>
                    <?php endif; ?>
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
