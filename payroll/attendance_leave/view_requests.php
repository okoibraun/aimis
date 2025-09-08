<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$res = $conn->query("SELECT lr.*, e.first_name FROM leave_requests lr 
JOIN employees e ON lr.employee_id = e.id ORDER BY lr.created_at DESC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | View Leave Request</title>
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

            <h4>Leave Requests</h4>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Employee</th><th>Type</th><th>Period</th><th>Reason</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php while($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['first_name'] ?></td>
                            <td><?= $row['leave_type'] ?></td>
                            <td><?= $row['start_date'] ?> to <?= $row['end_date'] ?></td>
                            <td><?= $row['reason'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td>
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <a href="update_leave.php?id=<?= $row['id'] ?>&status=Approved" class="btn btn-sm btn-success">Approve</a>
                                    <a href="update_leave.php?id=<?= $row['id'] ?>&status=Rejected" class="btn btn-sm btn-danger">Reject</a>
                                <?php else: ?>
                                    <span class="text-muted">Reviewed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

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