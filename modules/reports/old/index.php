<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

if (!isset($user_id)) {
    header('Location: /login.php');
    exit();
}

require_once 'functions.php';

$user_role = $_SESSION['user_role'];

// Fetch available reports based on access_roles
$reports = getReportsByRole($conn, $user_role);

// Fetch user dashboard (if exists)
$dashboard = getUserDashboard($conn, $user_id);
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
                    <h1>Reports Dashboard</h1>
                    <div class="pull-right">
                        <a href="builder.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> New Report</a>
                        <a href="schedule.php" class="btn btn-info btn-sm"><i class="fa fa-clock-o"></i> Scheduled Reports</a>
                    </div>
                </section>

                <section class="content">
                    <?php if ($dashboard): ?>
                        <div class="row" id="custom-dashboard">
                            <?php
                            $layout = json_decode($dashboard['layout'], true);
                            foreach ($layout as $widget):
                                $report = getReportById($conn, $widget['report_id']);
                                if ($report):
                            ?>
                                <div class="col-md-<?= $widget['width'] ?? 6 ?>">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><?= htmlspecialchars($report['name']) ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <canvas id="chart-<?= $report['id'] ?>"></canvas>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No custom dashboard yet. <a href="builder.php">Create one here</a>.</div>
                    <?php endif; ?>
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
    <script src="/plugins/chart.js/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        <?php foreach ($layout ?? [] as $widget): ?>
            fetch('ajax/fetch_data.php?id=<?= $widget['report_id'] ?>')
                .then(res => res.json())
                .then(data => {
                    new Chart(document.getElementById('chart-<?= $widget['report_id'] ?>'), data);
                });
        <?php endforeach; ?>
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
