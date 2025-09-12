<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// TOTAL OUTPUT
$output = mysqli_query($conn, "
    SELECT SUM(quantity_produced) AS total, SUM(quantity_defective) AS defective
    FROM production_output_logs
");
$out_data = mysqli_fetch_assoc($output);
$total_output = $out_data['total'] ?? 1;
$defective_output = $out_data['defective'];
$good_output = $total_output - $defective_output;

// TOTAL DOWNTIME (minutes)
$downtime = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(duration_minutes) AS total FROM production_downtime_logs
"))['total'] ?? 0;

// TOTAL ASSIGNED TIME (minutes)
$runtime = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(TIMESTAMPDIFF(MINUTE, assigned_start, assigned_end)) AS total
    FROM production_resource_assignments
"))['total'] ?? 1;

// IDEAL CYCLE TIME (hardcoded or from config)
$ideal_cycle_time = 1.0; // in minutes per unit

// --- OEE Components ---
$availability = ($runtime - $downtime) / $runtime;
$performance = ($ideal_cycle_time * $total_output) / ($runtime - $downtime);
$quality = $good_output / $total_output;

$oee = $availability * $performance * $quality * 100;
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Assignments</title>
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
                <section class="content-header mt-3 mb-3">
                    <h1>Production KPI Dashboard</h1>
                </section>

                <section class="content">
                    <div class="row">
                        <!-- OEE -->
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= round($oee, 2) ?>%</h3>
                                    <p>OEE (Overall Equipment Effectiveness)</p>
                                </div>
                                <div class="icon"><i class="fa fa-industry"></i></div>
                            </div>
                        </div>

                        <!-- Output -->
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= $total_output ?></h3>
                                    <p>Total Output</p>
                                </div>
                                <div class="icon"><i class="fa fa-box"></i></div>
                            </div>
                        </div>

                        <!-- Good Output -->
                        <div class="col-md-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?= $good_output ?></h3>
                                    <p>Good Output</p>
                                </div>
                                <div class="icon"><i class="fa fa-check-circle"></i></div>
                            </div>
                        </div>

                        <!-- Downtime -->
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?= $downtime ?> min</h3>
                                    <p>Downtime (Total)</p>
                                </div>
                                <div class="icon"><i class="fa fa-clock"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Trend Chart -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Daily Output Trend</h4>
                            <canvas id="outputChart"></canvas>
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
        <?php
            $trend = mysqli_query($conn, "
                SELECT DATE(produced_at) AS day, 
                    SUM(quantity_produced) AS produced, 
                    SUM(quantity_defective) AS defective
                FROM production_output_logs
                GROUP BY day ORDER BY day DESC LIMIT 7
            ");
            $labels = $produced = $defective = [];
            while ($t = mysqli_fetch_assoc($trend)) {
                $labels[] = $t['day'];
                $produced[] = $t['produced'];
                $defective[] = $t['defective'];
            }
        ?>

        const ctx = document.getElementById('outputChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_reverse($labels)) ?>,
                datasets: [
                    {
                        label: 'Produced',
                        data: <?= json_encode(array_reverse($produced)) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)'
                    },
                    {
                        label: 'Defective',
                        data: <?= json_encode(array_reverse($defective)) ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)'
                    }
                ]
            }
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
