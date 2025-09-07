<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $emp_id = $_POST['employee_id'];
  $date = $_POST['date'];
  $hours = $_POST['hours'];
  $rate = $_POST['rate'];

  $stmt = $conn->prepare("INSERT INTO overtime_records (employee_id, date, hours, rate, approved) VALUES (?, ?, ?, ?, 1)");
  $stmt->bind_param("isdd", $emp_id, $date, $hours, $rate);
  $stmt->execute();

  $msg = "Overtime logged.";
}

$employees = $conn->query("SELECT id, first_name FROM employees");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Record Overtime</title>
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

            <h4>Record Overtime</h4>
            <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
            <form method="post">
                <div class="form-group">
                    <label>Employee</label>
                    <select name="employee_id" class="form-control">
                        <?php while ($emp = $employees->fetch_assoc()): ?>
                            <option value="<?= $emp['id'] ?>"><?= $emp['first_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Hours Worked</label>
                    <input type="number" name="hours" step="0.5" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Rate per Hour</label>
                    <input type="number" name="rate" step="0.01" class="form-control" required>
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
