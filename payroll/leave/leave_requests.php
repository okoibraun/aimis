<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['submit_leave'])) {
    $emp_id = $_POST['employee_id'];
    $type = $_POST['leave_type'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, reason) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $emp_id, $type, $start, $end, $reason);
    $stmt->execute();
    header("Location: leave_requests.php?status=submitted");
    exit;
}
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

        <div class="container mt-4">
            <div class="card card-header mt-3 mb-4">
                <div class="row">
                    <div class="col-lg-6">
                        <h3>All Leave Requests</h3>
                    </div>
                    <div class="col-lg-6 text-end">
                        <a href="apply_leave.php" class="btn btn-primary float-end">Apply for Leave</a>
                    </div>
                </div>
            </div>
            <?php if (isset($_GET['status']) && $_GET['status'] == 'submitted'): ?>
                <div class="alert alert-success">Leave request submitted successfully.</div>
            <?php endif; ?>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT l.*, e.first_name, e.last_name 
                                        FROM leave_requests l
                                        JOIN employees e ON l.employee_id = e.id 
                                        ORDER BY l.id DESC");
                    while ($row = $res->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                            <td><?= ucfirst($row['leave_type']) ?></td>
                            <td><?= $row['start_date'] ?></td>
                            <td><?= $row['end_date'] ?></td>
                            <td><?= ucfirst($row['status']) ?></td>
                            <td>
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <a href="approve_leave.php?id=<?= $row['id'] ?>&action=Approved" class="btn btn-success btn-sm">Approve</a>
                                    <a href="approve_leave.php?id=<?= $row['id'] ?>&action=Rejected" class="btn btn-danger btn-sm">Reject</a>
                                <?php else: ?>
                                    <em><?= ucfirst($row['status']) ?></em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
