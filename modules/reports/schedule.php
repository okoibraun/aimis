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
$reports = getReportsByUser($conn, $user_id); // only reports user created or can access

$schedule_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$edit_mode = $schedule_id > 0;

if ($edit_mode) {
    $stmt = $conn->prepare("SELECT * FROM report_schedules WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $schedule_id, $user_id);
    $stmt->execute();
    $schedule = $stmt->get_result()->fetch_assoc();
}

$success = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];
    $frequency = $_POST['frequency'];
    $recipients = $_POST['recipients'];
    $format = $_POST['format'];
    $next_run = date('Y-m-d H:i:s', strtotime('tomorrow')); // default to tomorrow

    if ($edit_mode) {
        // Update Logic
        $stmt = $conn->prepare("UPDATE report_schedules SET report_id=?, frequency=?, recipients=?, format=? WHERE id=? AND user_id=?");
        $stmt->bind_param("isssii", $report_id, $frequency, $recipients, $format, $schedule_id, $user_id);

        if ($stmt->execute()) {
            $success = "Report scheduled updated successfully.";
        } else {
            $error = "Failed to update schedule report.";
        }
    } else { 
        //Insert Logic
        $stmt = $conn->prepare("INSERT INTO report_schedules (report_id, user_id, frequency, next_run, recipients, format, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("iissss", $report_id, $user_id, $frequency, $next_run, $recipients, $format);
        
        if ($stmt->execute()) {
            $success = "Report scheduled successfully.";
        } else {
            $error = "Failed to schedule report.";
        }
    }
}

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

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>Schedule Report</h1>
                </section>

                <section class="content">
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php elseif (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="box box-primary">
                            <div class="box-body">

                                <div class="form-group">
                                    <label>Select Report</label>
                                    <select name="report_id" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        <?php foreach ($reports as $r): ?>
                                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Frequency</label>
                                    <select name="frequency" class="form-control" required>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Recipients (comma-separated emails)</label>
                                    <input type="text" name="recipients" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Format</label>
                                    <select name="format" class="form-control" required>
                                        <option value="pdf" <?= ($schedule['format'] ?? '') === 'pdf' ? 'selected' : '' ?>>PDF</option>
                                        <option value="excel" <?= ($schedule['format'] ?? '') === 'excel' ? 'selected' : '' ?>>Excel</option>
                                        <option value="csv" <?= ($schedule['format'] ?? '') === 'csv' ? 'selected' : '' ?>>CSV</option>
                                    </select>
                                </div>

                            </div>
                            <div class="box-footer">
                                <button class="btn btn-success" type="submit">Schedule Report</button>
                            </div>
                        </div>
                    </form>
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
