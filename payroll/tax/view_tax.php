<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$sql = "SELECT t.*, e.first_name, e.last_name, e.employee_code 
        FROM tax_deductions t 
        JOIN employees e ON t.employee_id = e.id 
        ORDER BY t.deduction_date DESC";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll - View Tax</title>
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

            <div class="row mt-4 mb-4">
                <div class="col-lg-12">
                    <div class="float-end">
                        <a href="../employees/view_employee.php?id=<?= $_GET['employee_id']; ?>" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>Tax Deductions</h1>
                </section>
                <section class="content">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Tax Type</th>
                                        <th>Amount</th>
                                        <th>Period</th>
                                        <th>Deduction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['employee_code'] . ' - ' . $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                        <td><?= $row['tax_type'] ?></td>
                                        <td><?= number_format($row['amount'], 2) ?></td>
                                        <td><?= $row['period'] ?></td>
                                        <td><?= $row['deduction_date'] ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
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

<?php include '../../includes/footer.php'; ?>