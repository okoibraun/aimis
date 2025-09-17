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

$report_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$report = getReportById($conn, $report_id);

// Permissions check
$user_role = $_SESSION['role_id'] ?? 1;
if (!$report || !in_array($user_role, explode(',', $report['access_roles']))) {
    echo "<div class='alert alert-danger'>Access denied.</div>";
    require_once '../../includes/footer.phtml';
    exit;
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
                <section class="content-header mt-3 mb-3">
                    <h1><?= htmlspecialchars($report['name']) ?></h1>
                </section>

                <section class="content">
                    <div class="card card-primary">
                        <div class="card-body">
                            <canvas id="reportChart"></canvas>
                            <div id="kpi-alerts" class="p-3"></div>
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
    <script src="/plugins/chart.js/Chart.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <script>
        fetch('ajax/fetch_data.php?id=<?= $report_id ?>')
            .then(res => res.json())
            .then(config => {
                const chartData = config.data.datasets[0].data;
                const chartLabels = config.data.labels;

                // KPI Thresholds (from config object passed)
                const thresholds = config.options.thresholds || {};
                const alerts = [];

                if (thresholds.min !== undefined || thresholds.max !== undefined) {
                    chartData.forEach((val, i) => {
                        if ((thresholds.min && val < thresholds.min) || (thresholds.max && val > thresholds.max)) {
                            alerts.push(`${chartLabels[i]}: ${val} is ${val < thresholds.min ? 'below' : 'above'} threshold`);
                        }
                    });
                }

                if (alerts.length) {
                    document.getElementById('kpi-alerts').innerHTML =
                        `<div class="alert alert-warning"><strong>KPI Alert(s):</strong><ul>` +
                        alerts.map(a => `<li>${a}</li>`).join('') +
                        `</ul></div>`;
                }

                new Chart(document.getElementById('reportChart'), config);
            })
            .catch(err => {
                alert("Error loading chart data." + err);
                console.error(err);
            });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
