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
    $eid = $_POST['employee_id'];
    $basic = $_POST['basic_salary'];
    $house = $_POST['housing_allowance'];
    $transport = $_POST['transport_allowance'];
    $other = $_POST['other_allowances'];
    $deduct = $_POST['deductions'];
    $currency = $_POST['currency'];
    $effective = $_POST['effective_from'];

    $stmt = $conn->prepare("INSERT INTO salary_structures (employee_id, basic_salary, housing_allowance, transport_allowance, other_allowances, deductions, currency, effective_from)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idddddss", $eid, $basic, $house, $transport, $other, $deduct, $currency, $effective);

    if ($stmt->execute()) {
        header("Location: ../employees/list_employees.php?msg=salary_saved");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memos Dashboard</title>
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

            <h3>Setup Salary Structure</h3>
            <form action="save_structure.php" method="POST">
                <div class="form-group">
                    <label>Select Employee</label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- Choose --</option>
                        <?php
                        $res = $conn->query("SELECT id, first_name, last_name FROM employees");
                        while ($row = $res->fetch_assoc()):
                        ?>
                            <option value="<?= $row['id'] ?>"><?= $row['first_name'] . ' ' . $row['last_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Basic Salary</label>
                    <input type="number" step="0.01" name="basic_salary" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Housing Allowance</label>
                    <input type="number" step="0.01" name="housing_allowance" class="form-control">
                </div>
                <div class="form-group">
                    <label>Transport Allowance</label>
                    <input type="number" step="0.01" name="transport_allowance" class="form-control">
                </div>
                <div class="form-group">
                    <label>Other Allowances</label>
                    <input type="number" step="0.01" name="other_allowances" class="form-control">
                </div>
                <div class="form-group">
                    <label>Deductions</label>
                    <input type="number" step="0.01" name="deductions" class="form-control">
                </div>
                <div class="form-group">
                    <label>Currency</label>
                    <input type="text" name="currency" class="form-control" value="NGN">
                </div>
                <div class="form-group">
                    <label>Effective From</label>
                    <input type="date" name="effective_from" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Structure</button>
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
