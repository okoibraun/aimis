<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$company_id = $_SESSION['company_id'];

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Mark Attendance</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

        <div class="container mt-4">
            <h3>Mark Attendance</h3>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="attendance_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <button type="submit" name="load_employees" class="btn btn-info">Load Employees</button>
            </form>

            <?php
            if (isset($_POST['load_employees'])):
                $attendance_date = $_POST['attendance_date'];
                $employees = $conn->query("SELECT id, employee_code, first_name, last_name FROM employees WHERE status='active'");
            ?>
                <form method="POST" action="mark_attendance.php">
                    <input type="hidden" name="attendance_date" value="<?= $attendance_date ?>">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $employees->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                    <td>
                                        <select name="status[<?= $row['id'] ?>]" class="form-control">
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="leave">Leave</option>
                                            <option value="late">Late</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="remarks[<?= $row['id'] ?>]" class="form-control" placeholder="Optional">
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button type="submit" name="save_attendance" class="btn btn-primary">Save Attendance</button>
                </form>
            <?php endif; ?>

            <?php
            if (isset($_POST['save_attendance'])) {
                $date = $_POST['attendance_date'];
                foreach ($_POST['status'] as $emp_id => $status) {
                    $remark = $_POST['remarks'][$emp_id];
                    // $stmt = $conn->prepare("INSERT INTO attendance_records (employee_id, attendance_date, status, remarks) 
                                            // VALUES (?, ?, ?, ?)");
                    $stmt = $conn->prepare("INSERT INTO attendance (company_id, employee_id, date, status, remarks) 
                                            VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("iisss", $company_id, $emp_id, $date, $status, $remark);
                    $stmt->execute();
                }
                echo '<div class="alert alert-success mt-3">Attendance saved successfully!</div>';
            }
            ?>
        </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
