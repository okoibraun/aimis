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
    $employee_id = $_POST['employee_id'];
    $bank = $_POST['bank_name'];
    $acc_num = $_POST['account_number'];
    $acc_name = $_POST['account_name'];

    $stmt = $conn->prepare("UPDATE employees SET bank_name=?, account_number=?, account_name=? WHERE id=?");
    $stmt->bind_param("sssi", $bank, $acc_num, $acc_name, $employee_id);
    $stmt->execute();
    $msg = "Bank info updated.";
}

$employees = $conn->query("SELECT * FROM employees");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Update Bank Info</title>
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

            <h4>Update Employee Bank Info</h4>
            <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
            <form method="post">
                <div class="form-group">
                    <label>Employee</label>
                    <select name="employee_id" class="form-control" required>
                        <?php while ($row = $employees->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"><?= $row['first_name'] ?> <?= $row['last_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" name="account_name" class="form-control" required>
                </div>
                <button class="btn btn-primary">Update Info</button>
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