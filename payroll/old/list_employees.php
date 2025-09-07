<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$employees_result = mysqli_query($conn, "SELECT * FROM employees");
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
            <h1>Employees</h1>
          </section>

          <section class="content">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Employees</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Position</th>
                      <th>Salary</th>
                      <th>Tax Rate</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($employee = mysqli_fetch_assoc($employees_result)): ?>
                      <tr>
                        <td><?= htmlspecialchars($employee['first_name']) ?></td>
                        <td><?= htmlspecialchars($employee['position']) ?></td>
                        <td><?= number_format($employee['salary'], 2) ?></td>
                        <td><?= number_format($employee['tax_deductions'], 2) ?>%</td>
                        <td>
                          <a href="salary_tracking.php?employee_id=<?= $employee['id'] ?>" class="btn btn-info">View Salary</a>
                        </td>
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
