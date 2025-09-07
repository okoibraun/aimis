<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : 0;
$salary_data = null;

if ($employee_id > 0) {
    // Fetch employee salary details
    $result = mysqli_query($conn, "SELECT * FROM employees WHERE id = $employee_id");
    $salary_data = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $salary_data) {
    $payment_date = $_POST['payment_date'];
    $gross_salary = $salary_data['salary'];
    $tax_deduction = ($gross_salary * $salary_data['tax_deductions']) / 100;
    $net_salary = $gross_salary - $tax_deduction;

    // Insert salary payment into database
    $stmt = mysqli_prepare($conn, "
        INSERT INTO payroll (employee_id, payment_date, gross_salary, tax, net_salary)
        VALUES (?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'isddd', $employee_id, $payment_date, $gross_salary, $tax_deduction, $net_salary);
    mysqli_stmt_execute($stmt);
}
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
            <h1>Salary Tracking for <?= htmlspecialchars($salary_data['first_name']) ?></h1>
          </section>

          <section class="content">
          <?php if ($salary_data): ?>
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Salary Payment Details</h3>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-group">
                    <label>Payment Date</label>
                    <input type="date" name="payment_date" class="form-control" required>
                  </div>

                  <button type="submit" class="btn btn-primary">Record Payment</button>
                </form>

                <div class="mt-3">
                  <h4>Salary Breakdown</h4>
                  <p><strong>Gross Salary:</strong> $<?= number_format($salary_data['salary'], 2) ?></p>
                  <p><strong>Tax Deduction (<?= number_format($salary_data['tax_deductions'], 2) ?>%):</strong> $<?= number_format(($salary_data['salary'] * $salary_data['tax_deductions']) / 100, 2) ?></p>
                  <p><strong>Net Salary:</strong> $<?= number_format($salary_data['salary'] - ($salary_data['salary'] * $salary_data['tax_deductions']) / 100, 2) ?></p>
                </div>
              </div>
            </div>
          <?php endif; ?>
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
