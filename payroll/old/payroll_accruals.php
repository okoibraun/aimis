<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Insert payroll accruals for month-end or year-end
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accrual_date = $_POST['accrual_date'];

    // Query to generate accruals for all employees
    $employees_result = mysqli_query($conn, "SELECT * FROM employees");
    while ($employee = mysqli_fetch_assoc($employees_result)) {
        $accrued_salary = $employee['salary'];

        // Insert accrual entry into journal entries
        $stmt = mysqli_prepare($conn, "
            INSERT INTO journal_entries (entry_date, account_id, debit_amount, credit_amount, description)
            VALUES (?, ?, ?, ?, ?)
        ");
        $description = "Accrued salary for " . $employee['first_name'] . " " . $employee['last_name'] . " for " . date('F Y', strtotime($accrual_date));

        mysqli_stmt_bind_param($stmt, 'siiss', $accrual_date, $employee['id'], $accrued_salary, 0, $description);
        mysqli_stmt_execute($stmt);
    }
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
            <h1>Payroll Accruals</h1>
          </section>

          <section class="content">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Record Payroll Accrual</h3>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-group">
                    <label>Accrual Date</label>
                    <input type="date" name="accrual_date" class="form-control" required>
                  </div>

                  <button type="submit" class="btn btn-primary">Record Accruals</button>
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
