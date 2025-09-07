<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = $_SESSION['emp_id'];
    $type = $_POST['leave_type'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $emp_id, $type, $start, $end, $reason);
    $stmt->execute();
    $msg = "Leave request submitted.";
}

$employees = $conn->query("SELECT id, first_name FROM employees");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Request Leave</title>
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

            <h4>Request Leave</h4>
            <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
            <form method="post">
                <div class="form-group">
                    <label>Leave Type</label>
                    <select name="leave_type" class="form-control" required>
                        <option value="Casual">Casual</option>
                        <option value="Sick">Sick</option>
                        <option value="Vacation">Vacation</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" required></textarea>
                </div>
                <button class="btn btn-primary">Submit</button>
            </form>

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