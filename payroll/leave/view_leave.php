<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// if (!isset($_GET['employee_id'])) {
//     echo "<div class='alert alert-danger'>No employee ID provided.</div>";
//     exit;
// }

$employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : $_SESSION['employee_id'];

// Get employee info
$emp_sql = "SELECT first_name, last_name FROM employees WHERE id = $employee_id";
$emp_result = $conn->query($emp_sql);
if ($emp_result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Employee not found.</div>";
    exit;
}
$emp = $emp_result->fetch_assoc();

// Fetch leave records
$sql = "SELECT * FROM leave_requests WHERE employee_id = $employee_id ORDER BY start_date DESC";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | View Leave</title>
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
                <section class="content-header">
                    <h1>Leave History - <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></h1>
                </section>

                <section class="content">
                    <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Leave Applications</h3>
                        <div class="card-tools">
                            <?php if(isset($_GET['employee_id'])) { ?>
                            <a href="../employees/view_employee.php?id=<?= $employee_id ?>" class="btn btn-secondary btn-sm"><- Back</a>
                            <?php } else { ?>
                                <a href="/payroll/employees/employee" class="btn btn-secondary btn-sm"><- Back</a>
                                <a href="apply_leave.php?employee_id=<?= $_SESSION['employee_id']; ?>" class="btn btn-primary btn-sm">Apply for Leave</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <?php if ($result->num_rows > 0): ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                <td><?= ucfirst($row['leave_type']) ?></td>
                                <td><?= $row['start_date'] ?></td>
                                <td><?= $row['end_date'] ?></td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td>
                                    <?= $row['status']; ?>
                                </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="alert alert-info">No leave applications found for this employee.</div>
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
