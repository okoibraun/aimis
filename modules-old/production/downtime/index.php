<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}


$logs = $conn->query("
    SELECT dl.*, pwo.order_code, pr.name AS resource_name, u.name AS logger
    FROM production_downtime_logs dl
    JOIN production_work_orders pwo ON dl.work_order_id = pwo.id
    LEFT JOIN production_resources pr ON dl.resource_id = pr.id
    LEFT JOIN users u ON dl.logged_by = u.id
    ORDER BY dl.start_time DESC
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Downtime</title>
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

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>Downtime Logs</h1>
                    <a href="create.php" class="btn btn-primary">Log Downtime</a>
                </section>
                <section class="content">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Work Order</th>
                                <th>Resource</th>
                                <th>Reason</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Duration (min)</th>
                                <th>Logged By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($d = mysqli_fetch_assoc($logs)): ?>
                            <tr>
                                <td><?= $d['order_code'] ?></td>
                                <td><?= $d['resource_name'] ?? '-' ?></td>
                                <td><?= $d['downtime_reason'] ?></td>
                                <td><?= $d['start_time'] ?></td>
                                <td><?= $d['end_time'] ?></td>
                                <td><?= $d['duration_minutes'] ?></td>
                                <td><?= $d['logger'] ?? 'N/A' ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this log?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>
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
