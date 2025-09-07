<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = $_POST['employee_id'];
    $month = $_POST['month'];
    $amount = $_POST['amount'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO employee_bonuses (employee_id, month, amount, reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $emp_id, $month, $amount, $reason);
    $stmt->execute();

    $msg = "Bonus added.";
}

$employees = $conn->query("SELECT id, first_name FROM employees");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Record Bonus</title>
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

            <h4>Record Bonus</h4>
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
                    <label>Month</label>
                    <input type="month" name="month" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Bonus Amount</label>
                    <input type="number" name="amount" step="0.01" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" required></textarea>
                </div>
                <button class="btn btn-success">Submit</button>
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