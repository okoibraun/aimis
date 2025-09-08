<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$employee_added = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_name = mysqli_real_escape_string($conn, $_POST['employee_name']);
    $employee_position = mysqli_real_escape_string($conn, $_POST['employee_position']);
    $monthly_salary = floatval($_POST['monthly_salary']);
    $tax_rate = floatval($_POST['tax_rate']);

    // Insert employee into the database
    $stmt = mysqli_prepare($conn, "
        INSERT INTO employees (employee_name, employee_position, monthly_salary, tax_rate)
        VALUES (?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'ssdd', $employee_name, $employee_position, $monthly_salary, $tax_rate);
    $employee_added = mysqli_stmt_execute($stmt);
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
            <h1>Add Employee</h1>
          </section>

          <section class="content">
            <?php if ($employee_added): ?>
              <div class="alert alert-success">Employee added successfully.</div>
            <?php endif; ?>

            <!-- Add Employee Form -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Employee Details</h3>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-group">
                    <label>Employee Name</label>
                    <input type="text" name="employee_name" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Position</label>
                    <input type="text" name="employee_position" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Monthly Salary</label>
                    <input type="number" name="monthly_salary" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Tax Rate (%)</label>
                    <input type="number" name="tax_rate" class="form-control" required>
                  </div>

                  <button type="submit" class="btn btn-primary">Add Employee</button>
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
