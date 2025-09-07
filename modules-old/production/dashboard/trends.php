<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Daily Output Summary
$outputs = mysqli_query($conn, "
    SELECT DATE(produced_at) AS day,
           SUM(quantity_produced) AS produced,
           SUM(quantity_defective) AS defective
    FROM production_output_logs
    GROUP BY day ORDER BY day DESC LIMIT 14
");

// Daily Downtime Summary
$downtimes = [];
$res = mysqli_query($conn, "
    SELECT DATE(start_time) AS day, SUM(duration_minutes) AS downtime
    FROM production_downtime_logs
    GROUP BY day
");
while ($row = mysqli_fetch_assoc($res)) {
    $downtimes[$row['day']] = $row['downtime'];
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Dashboard</title>
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
                    <h1>Historical Performance Trends</h1>
                </section>

                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Output & Defects</h4>
                            <canvas id="outputChart"></canvas>
                        </div>
                    </div>
                    <br>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>OEE Trend (Approx.)</h4>
                            <canvas id="oeeChart"></canvas>
                        </div>
                    </div>
                </section>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            <?php
            $labels = $produced = $defective = $oee_values = [];

            $ideal_cycle_time = 1; // min/unit

            foreach ($outputs as $row) {
                $day = $row['day'];
                $labels[] = $day;
                $produced[] = $row['produced'];
                $defective[] = $row['defective'];
                $good = $row['produced'] - $row['defective'];

                $runtime = 0;
                $res_runtime = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT SUM(TIMESTAMPDIFF(MINUTE, assigned_start, assigned_end)) AS total
                    FROM production_resource_assignments
                    WHERE DATE(assigned_start) = '$day'
                "))['total'] ?? 1;

                $downtime = $downtimes[$day] ?? 0;
                $run_time = $res_runtime - $downtime;

                $availability = $run_time / max(1, $res_runtime);
                $performance = ($ideal_cycle_time * $row['produced']) / max(1, $run_time);
                $quality = $good / max(1, $row['produced']);

                $oee = $availability * $performance * $quality * 100;
                $oee_values[] = round($oee, 2);
            }
            ?>

            const outputCtx = document.getElementById('outputChart').getContext('2d');
            new Chart(outputCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_reverse($labels)) ?>,
                    datasets: [
                        {
                            label: 'Produced',
                            backgroundColor: 'rgba(40,167,69,0.7)',
                            data: <?= json_encode(array_reverse($produced)) ?>
                        },
                        {
                            label: 'Defective',
                            backgroundColor: 'rgba(220,53,69,0.7)',
                            data: <?= json_encode(array_reverse($defective)) ?>
                        }
                    ]
                }
            });

            const oeeCtx = document.getElementById('oeeChart').getContext('2d');
            new Chart(oeeCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_reverse($labels)) ?>,
                    datasets: [{
                        label: 'OEE (%)',
                        backgroundColor: 'rgba(0,123,255,0.4)',
                        borderColor: 'rgba(0,123,255,1)',
                        data: <?= json_encode(array_reverse($oee_values)) ?>,
                        fill: true
                    }]
                }
            });
            </script>

            <?php require_once '../../../templates/footer.php'; ?>


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
