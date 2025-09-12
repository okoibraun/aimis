<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}


// Dashboard Records
// Summary totals
$total_downtime = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(duration_minutes) AS total FROM production_downtime_logs
    WHERE company_id = $company_id
"))['total'] ?? 0;

$top_resources = mysqli_query($conn, "
    SELECT pr.name AS resource_name, SUM(dl.duration_minutes) AS total
    FROM production_downtime_logs dl
    LEFT JOIN production_resources pr ON dl.resource_id = pr.id
    WHERE dl.company_id = $company_id AND pr.company_id = dl.company_id
    GROUP BY dl.resource_id
    ORDER BY total DESC LIMIT 5
");

$top_reasons = mysqli_query($conn, "
    SELECT downtime_reason, SUM(duration_minutes) AS total
    FROM production_downtime_logs
    WHERE company_id = $company_id
    GROUP BY downtime_reason
    ORDER BY total DESC LIMIT 5
");

// Daily breakdown for last 7 days
$daily_breakdown = mysqli_query($conn, "
    SELECT DATE(start_time) AS day, SUM(duration_minutes) AS total
    FROM production_downtime_logs
    WHERE start_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND company_id = $company_id
    GROUP BY day ORDER BY day
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
                    <h1>Production Downtime Dashboard</h1>
                    <p>Total Downtime (All Time): <strong><?= number_format($total_downtime) ?> minutes</strong></p>
                </section>

                <section class="content">
                    <div class="row">
                        <!-- Downtime by Resource -->
                        <div class="col-md-6">
                            <h4>Top Resources by Downtime</h4>
                            <ul class="list-group">
                                <?php while($r = mysqli_fetch_assoc($top_resources)): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= $r['resource_name'] ?? 'Unassigned' ?>
                                        <span class="badge badge-danger"><?= $r['total'] ?> min</span>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>

                        <!-- Downtime Reasons -->
                        <div class="col-md-6">
                            <h4>Top Downtime Reasons</h4>
                            <ul class="list-group">
                                <?php while($r = mysqli_fetch_assoc($top_reasons)): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= $r['downtime_reason'] ?>
                                        <span class="badge badge-warning"><?= $r['total'] ?> min</span>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Daily Breakdown Chart -->
                        <div class="col-md-12">
                            <h4>Daily Downtime (Last 7 Days)</h4>
                            <canvas id="downtimeChart"></canvas>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('downtimeChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php
                    mysqli_data_seek($daily_breakdown, 0);
                    while ($row = mysqli_fetch_assoc($daily_breakdown)) echo "'" . $row['day'] . "',";
                ?>],
                datasets: [{
                    label: 'Downtime (minutes)',
                    data: [<?php
                        mysqli_data_seek($daily_breakdown, 0);
                        while ($row = mysqli_fetch_assoc($daily_breakdown)) echo $row['total'] . ",";
                    ?>],
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }]
            }
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
