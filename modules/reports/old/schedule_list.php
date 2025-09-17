<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

require_once 'functions.php';

$user_id = $_SESSION['user_id'];

$schedules = $conn->prepare("
    SELECT rs.*, r.name AS report_name 
    FROM report_schedules rs 
    JOIN reports r ON rs.report_id = r.id 
    WHERE rs.user_id = ? 
    ORDER BY rs.next_run ASC
");
$schedules->bind_param("i", $user_id);
$schedules->execute();
$result = $schedules->get_result();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Reports</title>
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

            <div class="content-wrapper mt-4">
                <section class="content-header">
                    <h1>My Scheduled Reports</h1>
                    <a href="schedule.php" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> Schedule New</a>
                </section>

                <section class="content">
                    <div class="box box-primary">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Report</th>
                                        <th>Format</th>
                                        <th>Frequency</th>
                                        <th>Recipients</th>
                                        <th>Last Run</th>
                                        <th>Next Run</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['report_name']) ?></td>
                                        <td><?= strtoupper($row['format']) ?></td>
                                        <td><?= ucfirst($row['frequency']) ?></td>
                                        <td><?= htmlspecialchars($row['recipients']) ?></td>
                                        <td><?= $row['last_run'] ?? '-' ?></td>
                                        <td><?= $row['next_run'] ?></td>
                                        <td>
                                            <span class="label label-<?= $row['status'] === 'active' ? 'success' : 'default' ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="schedule.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>
                                            <a href="schedule_delete.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this schedule?')"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
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
