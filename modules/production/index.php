<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Check User Permissions
// $page = "list";
// $user_permissions = get_user_permissions($_SESSION['user_id']);

// if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
//     die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
//     exit;
// }

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

$costings = $conn->query("SELECT 
        SUM(estimated_cost) AS total_estimated_cost, 
        SUM(actual_cost) AS total_actual_cost, 
        SUM(cost_variance) AS total_cost_variance
        FROM production_work_orders
        WHERE company_id = $company_id AND MONTH(created_at) = MONTH(CURDATE())
            AND YEAR(created_at) = YEAR(CURDATE())")->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Productions Dashboard</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="hold-transition layout-fixed sidebar-expand-lg bg-body-tertiary">
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
                <!-- Content Header (Page header) -->
                <div class="content-header mt-3 mb-3">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">Production <small>Dashboard</small></h1>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-end mt-3">
                                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                                    <li class="breadcrumb-item active">Production</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!--/.container-fluid -->
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <!-- Info Box -->
                        <div class="row">

                            <div class="col-md-3">
                                <!-- Info Boxes Style 2 -->
                                <div class="info-box mb-3 bg-success text-white">
                                    <span class="info-box-icon bg-white text-success">
                                        <i class="fas fa-industry"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span>
                                            Overall Equipment Effectiveness
                                            <strong class="float-end"><?= round($oee, 2) ?>%</strong>
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="col-md-3">
                                <div class="info-box mb-3 bg-info text-white">
                                    <span class="info-box-icon bg-white text-info"><i class="fas fa-box"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Output</span>
                                        <span class="info-box-number"><?= $total_output ?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="col-md-3">
                                <div class="info-box mb-3 bg-primary text-white">
                                    <span class="info-box-icon bg-white text-primary"><i class="fas fa-check-circle"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Good Output</span>
                                        <span class="info-box-number"><?= $good_output ?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="col-md-3">
                                <div class="info-box mb-3 text-white bg-danger">
                                    <span class="info-box-icon bg-white text-danger"><i class="fas fa-clock"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Downtime</span>
                                        <span class="info-box-number"><?= $downtime ?> min</span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.Info Box -->

                        <div class="row mt-3 mb-3">
                            <div class="col">
                                <div class="card card-primary">
                                    <div class="card-body">
                                        <h5>Daily Output Trend</h5>
                                        <div class="chart">
                                            <canvas id="outputChart" height="540" style="height: 180px; display: block; width: 359px;" width="1077" class="chartjs-render-monitor"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card card-success">
                                    <div class="card-body">
                                        <h5 class="text-center">
                                             Production Costing Summary: <?= date('F') ?>, <?= date('Y') ?>
                                        </h5>

                                        <div class="chart">
                                            <!-- Sales Chart Canvas -->
                                            <canvas id="salesChart" height="540" style="height: 260px; display: block; width: 359px;" width="1077" class="chartjs-render-monitor"></canvas>
                                        </div>
                                        <!-- /.chart-responsive -->
                                    </div>
                                    <!-- ./card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                        <!-- /.row -->

                        <div class="row">
                            <!-- Costing Summary -->
                            <div class="col-md-7">
                                <div class="card">
                                    <?php
                                        $work_orders = $conn->query("SELECT id, order_code, estimated_cost, actual_cost, cost_variance 
                                            FROM production_work_orders
                                            WHERE company_id = $company_id
                                            ORDER BY created_at DESC
                                            LIMIT 8
                                        ");
                                    ?>
                                    <div class="card-header">
                                        <h3 class="card-title">Costing Summary</h3>
                                        <div class="card-tools">
                                            <a href="/modules/production/costing/" class="btn btn-info btn-sm">View All</a>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Order Code</th>
                                                    <th>Estimated</th>
                                                    <th>Actual</th>
                                                    <th>Variance</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($work_orders as $w): ?>
                                                <tr>
                                                    <td><?= $w['order_code'] ?></td>
                                                    <td>₦<?= number_format($w['estimated_cost'], 2) ?></td>
                                                    <td>₦<?= number_format($w['actual_cost'], 2) ?></td>
                                                    <td><?= $w['cost_variance'] >= 0 ? '+' : '-' ?>₦<?= number_format(abs($w['cost_variance']), 2) ?></td>
                                                    <td><a href="breakdown.php?work_order_id=<?= $w['id'] ?>" class="btn btn-sm btn-primary">Breakdown</a></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- /.col -->
                            <!-- Production Recap -->
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Production Recap</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="chart-responsive">
                                                    <div class="chartjs-size-monitor">
                                                        <div class="chartjs-size-monitor-expand">
                                                            <div class=""></div>
                                                        </div>
                                                        <div class="chartjs-size-monitor-shrink">
                                                            <div class=""></div>
                                                        </div>
                                                    </div>
                                                    <canvas id="pieChart" height="537" width="1077" style="display: block; height: 359px; width: 359px;" class="chartjs-render-monitor"></canvas>
                                                </div>
                                                <!-- ./chart-responsive -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-md-5">
                                                <ul class="chart-legend clearfix">
                                                    <li><i class="far fa-circle text-success"></i> BOM</li>
                                                    <li><i class="far fa-circle text-warning"></i> Work Orders</li>
                                                    <li><i class="far fa-circle text-secondary"></i> Costings</li>
                                                    <li><i class="far fa-circle text-info"></i> Requisitions</li>
                                                    <li><i class="far fa-circle" style="color: #040342;"></i> Resources</li>
                                                    <li><i class="far fa-circle" style="color: #700687;"></i> Assignments</li>
                                                    <li><i class="far fa-circle" style="color: #da0505;"></i> QC</li>
                                                    <li><i class="far fa-circle" style="color: #ff00ff;"></i> Downtime</li>
                                                    <li><i class="far fa-circle" style="color: #3e5620;"></i> Output</li>
                                                </ul>
                                            </div>
                                            <!-- /.col -->
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                        <!-- /.row -->

                        <!-- Main row -->
                        <div class="row mt-3 mb-3">
                            <!-- Left col -->
                            <div class="col-md-12">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <!-- CUSTOMERS LIST -->
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Latest Requisitions</h3>
                                                <div class="card-tools">
                                                    <a href="/modules/production/requisitions/" class="btn btn-info btn-sm">View All</a>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body p-0">
                                                <?php
                                                    $result = $conn->query("SELECT pr.*, pwo.order_code
                                                        FROM production_requisitions pr
                                                        JOIN production_work_orders pwo ON pr.work_order_id = pwo.id
                                                        WHERE pr.company_id = $company_id AND pwo.company_id = $company_id
                                                        ORDER BY pr.created_at DESC
                                                        LIMIT 8
                                                    ");
                                                ?>
                                                <table class="table table-hover table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Requisition Code</th>
                                                            <th>Work Order</th>
                                                            <th>Date</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($result as $r): ?>
                                                        <tr>
                                                            <td><?= $r['requisition_code'] ?></td>
                                                            <td><?= $r['order_code'] ?></td>
                                                            <td><?= $r['created_at'] ?></td>
                                                            <td>
                                                                <a href="view.php?id=<?= $r['id'] ?>" class="text-info"><i class="fas fa-eye"></i></a>
                                                                <a href="edit.php?id=<?= $r['id'] ?>"><i class="fas fa-edit"></i></a>
                                                                <a href="delete.php?id=<?= $r['id'] ?>" class="text-danger" onclick="return confirm('Delete this requisition?')"><i class="fas fa-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <!-- /.customer-list -->
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                        <!--/.card -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-6">
                                        <!-- LEADS LIST -->
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Latest Resources</h3>
                                                <div class="card-tools">
                                                    <a href="/modules/production/resources/" class="btn btn-info btn-sm">View All</a>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body p-0 table-responsive">
                                                <?php $result = $conn->query("SELECT * FROM production_resources WHERE company_id = $company_id ORDER BY name ASC LIMIT 8"); ?>
                                                <table class="table table-hover table-striped">
                                                    <thead>
                                                        <tr>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Code</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($result as $r): ?>
                                                        <tr>
                                                            <td><?= $r['name'] ?></td>
                                                            <td><?= $r['type'] ?></td>
                                                            <td><?= $r['code'] ?></td>
                                                            <td><?= $r['status'] ?></td>
                                                            <td>
                                                                <a href="edit.php?id=<?= $r['id'] ?>" class="text-info"><i class="fas fa-eye"></i></a>
                                                                <a href="delete.php?id=<?= $r['id'] ?>" class="text-danger" onclick="return confirm('Delete this resource?')"><i class="fas fa-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <!-- /.Lead-list -->
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->
                                    </div>
                                </div>
                                <!-- /.row -->

                                <!-- TABLE: LATEST WORK ORDERS -->
                                <div class="card">
                                    <div class="card-header border-transparent">
                                        <h3 class="card-title">Latest Work Orders</h3>
                                        <div class="card-tools">
                                            <a href="/modules/production/work_orders/" class="btn btn-sm btn-info">View All</a>
                                            <a href="/modules/production/work_orders/create.php" class="btn btn-primary btn-sm">Create Work Order</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0 table-responsive">
                                        <?php
                                            $result = $conn->query("SELECT pwo.*, ip.name AS product_name
                                            FROM production_work_orders pwo
                                            JOIN sales_products ip ON pwo.product_id = ip.id
                                            WHERE pwo.company_id = $company_id AND ip.company_id = $company_id
                                            ORDER BY pwo.created_at DESC LIMIT 8");
                                        ?>
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order Code</th>
                                                    <th>Product</th>
                                                    <th>Qty</th>
                                                    <th>Status</th>
                                                    <th>Start</th>
                                                    <th>End</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($result as $row): ?>
                                                <tr>
                                                    <td><?= $row['order_code'] ?></td>
                                                    <td><?= $row['product_name'] ?></td>
                                                    <td><?= $row['quantity'] ?></td>
                                                    <td><?= $row['status'] ?></td>
                                                    <td><?= $row['scheduled_start'] ?></td>
                                                    <td><?= $row['scheduled_end'] ?></td>
                                                    <td>
                                                        <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this work order?')"><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <!-- /.table-responsive -->
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!--/. container-fluid -->
                </section>
                <!-- /.content -->
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
    <script>
        /* global Chart:false */
        $(function () {
            'use strict'

            /* ChartJS
            * -------
            * Here we will create a few charts using ChartJS
            */

            //-----------------------
            // - MONTHLY SALES CHART -
            //-----------------------

            // Get context with jQuery - using jQuery's .get() method.
            var salesChartCanvas = $('#salesChart').get(0).getContext('2d')

            var salesChartData = {
                labels: ['<?= date('F') ?>'],
                datasets: [
                    {
                        label: 'Estimated',
                        backgroundColor: 'red',
                        data: [<?= $costings['total_estimated_cost'] > 0 ? $costings['total_estimated_cost'] : 0 ?>]
                    },
                    {
                        label: 'Actuals',
                        backgroundColor: 'green',
                        data: [<?= $costings['total_actual_cost'] > 0 ? $costings['total_actual_cost'] : 0 ?>]
                    },
                    {
                        label: 'Variance',
                        backgroundColor: 'blue',
                        data: [<?= $costings['total_cost_variance'] > 0 ? $costings['total_cost_variance'] : 0 ?>]
                    }
                ]
            }

            var salesChartOptions = {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                display: true
                },
                scales: {
                xAxes: [{
                    gridLines: {
                    display: true
                    }
                }],
                yAxes: [{
                    gridLines: {
                    display: true
                    }
                }]
                }
            }

            // This will get the first returned node in the jQuery collection.
            // eslint-disable-next-line no-unused-vars
            var salesChart = new Chart(salesChartCanvas, {
                type: 'bar',
                data: salesChartData,
                options: salesChartOptions
            }
            )

            //---------------------------
            // - END MONTHLY SALES CHART -
            //---------------------------

            //-------------
            // - PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieData = {
                labels: [
                'BOM',
                'Work Orders',
                'Costings',
                'Requisitions',
                'Resources',
                'Assignments',
                'QC',
                'Downtime',
                'Outputs'
                ],
                datasets: [
                {
                    data: [
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_bom WHERE company_id = $company_id")->fetch_assoc()['total']; ?>, 
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_work_orders WHERE company_id = $company_id")->fetch_assoc()['total']; ?>, 
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_work_orders WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_requisitions WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_resources WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_resource_assignments WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_qc_checkpoints WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_downtime_logs WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_output_logs WHERE company_id = $company_id")->fetch_assoc()['total']; ?>
                    ],
                    backgroundColor: ['#00a65a', '#f39c12', '#6c757d', '#3c8dbc', '#040342', '#700687', '#da0505', '#ff00ff', '#3e5620']
                }
                ]
            }
            var pieOptions = {
                legend: {
                display: false
                }
            }
            // Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            // eslint-disable-next-line no-unused-vars
            var pieChart = new Chart(pieChartCanvas, {
                type: 'doughnut',
                data: pieData,
                options: pieOptions
            })

            //-----------------
            // - END PIE CHART -
            //-----------------
        })

        // lgtm [js/unused-local-variable]
        <?php
            $trend = $conn->query("
                SELECT DATE(produced_at) AS day, 
                    SUM(quantity_produced) AS produced, 
                    SUM(quantity_defective) AS defective
                FROM production_output_logs
                WHERE company_id = $company_id
                GROUP BY day ORDER BY day DESC LIMIT 7
            ");
            $labels = $produced = $defective = [];
            foreach($trend as $t) {
                $labels[] = $t['day'];
                $produced[] = $t['produced'];
                $defective[] = $t['defective'];
            }
        ?>

        const ctx = document.querySelector('#outputChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_reverse($labels)) ?>,
                datasets: [
                    {
                        label: 'Produced',
                        data: [<?= json_encode(array_reverse($produced)) ?>],
                        backgroundColor: 'rgba(54, 162, 235, 0.8)'
                    },
                    {
                        label: 'Defective',
                        data: [<?= json_encode(array_reverse($defective)) ?>],
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
