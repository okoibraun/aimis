<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$company_id = $_SESSION['company_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    foreach ($_POST['status'] as $emp_id => $status) {
        $stmt = $conn->prepare("INSERT INTO attendance (company_id, employee_id, date, status) VALUES (?, ?, ?, ?) 
                                ON DUPLICATE KEY UPDATE status=?");
        $stmt->bind_param("issss", $company_id, $emp_id, $date, $status, $status);
        $stmt->execute();
    }
    $msg = "Attendance saved.";
}

$employees = $conn->query("SELECT * FROM employees WHERE status='active'");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Mark Attendance</title>
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
                <div class="content-header mt-4 mb-4">
                    <h2>Attendance</h2>
                </div>
                <div class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Mark Attendance
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
                            <form method="post">
                                <div class="row mb-4">
                                    <div class="col-auto">
                                        <label>Date: </label>
                                    </div>
                                    <div class="col-auto">
                                        <div class="form-group">
                                            <input type="date" name="date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                </div>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $employees->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['employee_code'] ?> - <?= $row['first_name'] ?> <?= $row['last_name']; ?></td>
                                                <td>
                                                    <select name="status[<?= $row['id'] ?>]" class="form-control">
                                                        <option value="Present">Present</option>
                                                        <option value="Absent">Absent</option>
                                                        <option value="On Leave">On Leave</option>
                                                        <option value="Late">Late</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <button class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
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