<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// List all Downtime Logs
$logs = $conn->query("
    SELECT dl.*, pwo.order_code, pr.name AS resource_name, u.name AS logger
    FROM production_downtime_logs dl
    JOIN production_work_orders pwo ON dl.work_order_id = pwo.id
    LEFT JOIN production_resources pr ON dl.resource_id = pr.id
    LEFT JOIN users u ON dl.logged_by = u.id
    WHERE dl.company_id = $company_id AND pwo.company_id = dl.company_id AND pr.company_id = dl.company_id AND u.company_id = dl.company_id
    ORDER BY dl.start_time DESC
");

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
                <section class="content-header mt-3 mb-3">
                    <h3>Production Downtime</h3>
                    <p>Total Downtime (All Time): <strong><?= number_format($total_downtime) ?> minutes</strong></p>
                </section>

                <section class="content">
                    
                    <div class="row">
                        <!-- Downtime by Resource -->
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Top Resources by Downtime</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <?php while($r = mysqli_fetch_assoc($top_resources)): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= $r['resource_name'] ?? 'Unassigned' ?>
                                                <span class="badge badge-danger"><?= $r['total'] ?> min</span>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Downtime Reasons -->
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Tope Downtime Reasons</h3>
                                </div>
                                <div class="card-body">
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
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Daily Breakdown Chart -->
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Daily Downtime (Last 7 Days)</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="downtimeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Downtime Logs</h3>
                                <div class="card-tools">
                                    <a href="create.php" class="btn btn-primary">Log Downtime</a>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-bordered DataTable">
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
                            </div>
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
