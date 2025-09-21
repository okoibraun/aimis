<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');
include("../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
// $page = "list";
// $user_permissions = get_user_permissions($_SESSION['user_id']);

// if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
//     die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
//     exit;
// }
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Payroll Reports</title>
    <?php include_once("../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="hold-transition layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../includes/sidebar.phtml"); ?>
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
                                <h1 class="m-0">Payroll</h1>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-end mt-3">
                                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                                    <li class="breadcrumb-item active">Reports</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!--/.container-fluid -->
                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box mb-3">
                                    <span class="info-box-icon bg-success elevation-1 text-white">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Employees</span>
                                        <?php $total_employees = $conn->query("SELECT COUNT(*) AS total FROM employees WHERE company_id = $company_id")->fetch_assoc()['total']; ?>
                                        <span class="info-box-number"><?= $total_employees ?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning elevation-1 text-white">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Leaves</span>
                                    <span class="info-box-number"><?= $conn->query("SELECT COUNT(*) AS total FROM leave_requests WHERE company_id = $company_id")->fetch_assoc()['total']; ?></span>
                                </div>
                                <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box mb-3">
                                    <span class="info-box-icon bg-primary elevation-1 text-white">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Contracts</span>
                                        <span class="info-box-number">
                                            <?= $conn->query("SELECT COUNT(*) AS total FROM contracts WHERE company_id = $company_id")->fetch_assoc()['total']; ?>
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box mb-3">
                                    <span class="info-box-icon bg-danger elevation-1 text-white">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Compliance</span>
                                        <span class="info-box-number"><?= $conn->query("SELECT COUNT(*) AS total FROM employee_tax_compliance WHERE company_id = $company_id")->fetch_assoc()['total']; ?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="info-box mb-3">
                                    <span class="info-box-icon bg-info elevation-1 text-white">
                                        <i class="fas fa-user-tie"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <div class="progress-group">
                                            <span class="progress-text">Attendance</span>
                                            <?php $date = date('Y-m-d'); $attendance = $conn->query("SELECT COUNT(*) AS total FROM attendance WHERE company_id = $company_id AND date = '$date' AND status = 'Present' OR status = 'Late'")->fetch_assoc()['total']; ?>
                                            <span class="float-end">
                                                <b><?= $attendance ?></b>/<?= $total_employees ?>
                                            </span>
                                            <div class="progress progress-md">
                                                <div class="progress-bar bg-danger" style="width: <?= $attendance || $total_employees ? $attendance / $total_employees * 100 : 0 ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <!-- /.col -->
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Monthly Recap Report</h5>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <p class="text-center">
                                                    <strong>Salary statistics for: <?= date('F', strtotime('-1 month')) . ", " . date('Y'); ?></strong>
                                                </p>

                                                <div class="chart">
                                                    <div class="chartjs-size-monitor">
                                                        <div class="chartjs-size-monitor-expand">
                                                            <div class=""></div>
                                                        </div>
                                                        <div class="chartjs-size-monitor-shrink">
                                                            <div class=""></div>
                                                        </div>
                                                    </div>
                                                    <!-- Sales Chart Canvas -->
                                                    <canvas id="salesChart" height="540" style="height: 180px; display: block; width: 359px;" width="1077" class="chartjs-render-monitor"></canvas>
                                                </div>
                                                <!-- /.chart-responsive -->
                                            </div>
                                            <!-- /.col -->
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                    <!-- ./card-body -->
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-sm-3 col-6">
                                                <div class="description-block border-right">
                                                    <!-- <span class="description-percentage text-success">
                                                        <i class="fas fa-caret-up"></i> 17%
                                                    </span> -->
                                                    <?php
                                                        $payslip = $conn->query("SELECT 
                                                        SUM(allowances) AS total_allowances, 
                                                        SUM(bonuses) AS total_bonuses, 
                                                        SUM(deductions) AS total_deductions, 
                                                        SUM(net_salary) AS total_net
                                                        FROM payslips
                                                        WHERE company_id = $company_id AND generated_at BETWEEN 
                                                            DATE_SUB(LAST_DAY(CURDATE()), INTERVAL 1 MONTH) + INTERVAL 1 DAY
                                                            AND LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))")->fetch_assoc();
                                                    ?>
                                                    <h5 class="description-header">N<?= $payslip['total_net'] > 0 ? number_format($payslip['total_net'], 2) : 0; ?></h5>
                                                    <span class="description-text">NET SALARY</span>
                                                </div>
                                                <!-- /.description-block -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-sm-3 col-6">
                                                <div class="description-block border-right">
                                                    <!-- <span class="description-percentage text-warning">
                                                        <i class="fas fa-caret-left"></i> 0%
                                                    </span> -->
                                                    <h5 class="description-header">N<?= $payslip['total_bonuses'] > 0 ? number_format($payslip['total_bonuses'], 2) : 0; ?></h5>
                                                    <span class="description-text">BONUSES</span>
                                                </div>
                                                <!-- /.description-block -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-sm-3 col-6">
                                                <div class="description-block border-right">
                                                    <!-- <span class="description-percentage text-success">
                                                        <i class="fas fa-caret-up"></i> 20%
                                                    </span> -->
                                                    <h5 class="description-header">N<?= $payslip['total_allowances'] > 0 ? number_format($payslip['total_allowances'], 2) : 0; ?></h5>
                                                    <span class="description-text">ALLOWANCES</span>
                                                </div>
                                                <!-- /.description-block -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-sm-3 col-6">
                                                <div class="description-block">
                                                    <!-- <span class="description-percentage text-danger">
                                                        <i class="fas fa-caret-down"></i> 18%
                                                    </span> -->
                                                    <h5 class="description-header">N<?= $payslip['total_deductions'] > 0 ? number_format($payslip['total_deductions'], 2) : 0; ?></h5>
                                                    <span class="description-text">DEDUCTIONS</span>
                                                </div>
                                                <!-- /.description-block -->
                                            </div>
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                    <!-- /.card-footer -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                             <div class="col-md-4">
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <p class="text-center">
                                            <?php 
                                                $total_leaves = $conn->query("SELECT COUNT(id) AS total FROM leave_requests WHERE company_id = $company_id")->fetch_assoc();
                                                $total_pending = $conn->query("SELECT COUNT(id) AS total FROM leave_requests WHERE status = 'Pending' AND company_id = $company_id")->fetch_assoc();
                                                $total_approved = $conn->query("SELECT COUNT(id) AS total FROM leave_requests WHERE status = 'Approved' AND company_id = $company_id")->fetch_assoc();
                                                $total_rejected = $conn->query("SELECT COUNT(id) AS total FROM leave_requests WHERE status = 'Rejected' AND company_id = $company_id")->fetch_assoc();
                                            ?>
                                            <strong>Leaves Status</strong>
                                        </p>

                                        <div class="progress-group">
                                            <?php if($total_pending['total'] > 0 && $total_leaves['total'] > 0) { ?>
                                            <span class="progress-text">New Leave Request</span>
                                            <span class="float-end">
                                                <b><?= $total_pending['total'] ?></b>/<?= $total_leaves['total'] ?>
                                            </span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-info" style="width: <?= $total_pending['total'] / $total_leaves['total'] * 100 ?>%"></div>
                                            </div>
                                            <?php } else { ?>
                                            <span class="progress-text">New Leave Request</span>
                                            <span class="float-end">
                                                <b>0</b>/0
                                            </span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-info" style="width: 0%"></div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <!-- /.progress-group -->

                                        <div class="progress-group">
                                            <?php if($total_approved['total'] > 0 && $total_leaves['total'] > 0) { ?>
                                            <span class="progress-text">Approved Leave Requests</span>
                                            <span class="float-end">
                                                <b><?= $total_approved['total'] ?></b>/<?= $total_leaves['total'] ?>
                                            </span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success" style="width: <?= $total_approved['total'] / $total_leaves['total'] * 100 ?>%"></div>
                                            </div>
                                            <?php } else { ?>
                                            <span class="progress-text">Approved Leave Requests</span>
                                            <span class="float-end">
                                                <b>0</b>/0
                                            </span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success" style="width: 0%"></div>
                                            </div>
                                            <?php } ?>
                                        </div>

                                        <!-- /.progress-group -->
                                        <div class="progress-group">
                                            <?php if($total_rejected['total'] > 0 && $total_leaves['total'] > 0) { ?>
                                            <span class="progress-text">Rejected Leave Requests</span>
                                            <span class="float-end">
                                                <b><?= $total_rejected['total'] ?></b>/<?= $total_rejected['total'] ?>
                                            </span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-danger" style="width: <?= $total_rejected['total'] / $total_leaves['total'] * 100 ?>%"></div>
                                            </div>
                                            <?php } else { ?>
                                            <span class="progress-text">Rejected Leave Requests</span>
                                            <span class="float-end">
                                                <b>0</b>/0
                                            </span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-danger" style="width: 0%"></div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <!-- /.progress-group -->
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Attendance Rate</h3>
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
                                                    <canvas id="pieChart" height="537" width="1077" style="display: block; height: 179px; width: 359px;" class="chartjs-render-monitor"></canvas>
                                                </div>
                                                <!-- ./chart-responsive -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-md-5">
                                                <ul class="chart-legend clearfix">
                                                    <li><i class="far fa-circle text-success"></i> Present</li>
                                                    <li><i class="far fa-circle text-danger"></i> Absent</li>
                                                    <li><i class="far fa-circle text-warning"></i> Late</li>
                                                </ul>
                                            </div>
                                            <!-- /.col -->
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                    <!-- /.card-body -->
                                    <!-- <div class="card-footer p-0">
                                        <ul class="nav nav-pills flex-column">
                                            <li class="nav-item">
                                                <a href="#" class="nav-link">
                                                United States of America
                                                <span class="float-end text-danger">
                                                    <i class="fas fa-arrow-down text-sm"></i>
                                                    12%</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#" class="nav-link">
                                                India
                                                <span class="float-end text-success">
                                                    <i class="fas fa-arrow-up text-sm"></i> 4%
                                                </span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#" class="nav-link">
                                                China
                                                <span class="float-end text-warning">
                                                    <i class="fas fa-arrow-left text-sm"></i> 0%
                                                </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div> -->
                                    <!-- /.footer -->
                                </div>
                                <!-- /.card -->
                             </div>
                        </div>
                        <!-- /.row -->

                        <!-- Main row -->
                        <div class="row mt-3 mb-3">
                            <!-- Left col -->
                            <div class="col-8">
                                <!-- TABLE: LATEST ORDERS -->
                                <div class="card">
                                    <div class="card-header border-transparent">
                                        <h3 class="card-title">Employees</h3>

                                        <div class="card-tools">
                                            <a href="/payroll/employees/list_employees.php" class="btn-sm btn-link">View All</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0 table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Job Title</th>
                                                    <th>Department</th>
                                                    <th>Phone</th>
                                                    <th>Email</th>
                                                    <th>Country</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $res = $conn->query("SELECT * FROM employees WHERE company_id = $company_id ORDER BY created_at DESC LIMIT 8");
                                                foreach ($res as $row):
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['employee_code']) ?></td>
                                                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['position']) ?></td>
                                                    <td><?= $row['department'] ?></td>
                                                    <td><?= $row['phone'] ?></td>
                                                    <td><?= $row['email'] ?></td>
                                                    <td><?= htmlspecialchars($row['country']) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->

                            <div class="col-md-4">
                                <!-- LEAVE REQUESTS -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Leave Requests</h3>
                                        <div class="card-tools">
                                            <a href="/payroll/leave/leave_requests.php" class="uppercase">View All</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0 table-responsive">
                                        
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $res = $conn->query("SELECT l.*, e.first_name, e.last_name 
                                                                    FROM leave_requests l
                                                                    JOIN employees e ON l.employee_id = e.id
                                                                    WHERE l.company_id = $company_id AND e.company_id = l.company_id
                                                                    ORDER BY l.id DESC");
                                                foreach ($res as $row):
                                                ?>
                                                    <tr>
                                                        <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                                        <td><?= ucfirst($row['leave_type']) ?></td>
                                                        <td><?= ucfirst($row['status']) ?></td>
                                                        <td>
                                                            <?php if ($row['status'] == 'Pending'): ?>
                                                                <a href="approve_leave.php?id=<?= $row['id'] ?>&action=Approved" class="btn btn-success btn-sm">Approve</a>
                                                                <a href="approve_leave.php?id=<?= $row['id'] ?>&action=Rejected" class="btn btn-danger btn-sm">Reject</a>
                                                            <?php else: ?>
                                                                <em><?= ucfirst($row['status']) ?></em>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div><!--/. container-fluid -->
                </section>
                <!-- /.content -->
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../includes/scripts.phtml"); ?>
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
                labels: ['<?= date('F', strtotime('-1 month')) ?>'],
                datasets: [
                    {
                        label: 'NET SALARY',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        data: [<?= $payslip['total_net'] ?>]
                    },
                    {
                        label: 'BONUSES',
                        backgroundColor: 'green',
                        data: [<?= $payslip['total_bonuses'] ?>]
                    },
                    {
                        label: 'ALLOWANCES',
                        backgroundColor: 'red',
                        data: [<?= $payslip['total_allowances'] ?>]
                    },
                    {
                        label: 'DEDUCTIONS',
                        backgroundColor: 'yellow',
                        data: [<?= $payslip['total_deductions'] ?>]
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
                labels: ['Present', 'Absent', 'Late'],
                datasets: [
                    {
                        data: [
                            <?php $date = date('Y-m-d'); echo $conn->query("SELECT COUNT(id) AS total FROM attendance WHERE date = '$date' AND status = 'Present' AND company_id = $company_id")->fetch_assoc()['total']; ?>, 
                            <?= $conn->query("SELECT COUNT(id) AS total FROM attendance WHERE date = '$date' AND status = 'Absent' AND company_id = $company_id")->fetch_assoc()['total']; ?>, 
                            <?= $conn->query("SELECT COUNT(id) AS total FROM attendance WHERE date = '$date' AND status = 'Late' AND company_id = $company_id")->fetch_assoc()['total']; ?>
                        ],
                        backgroundColor: ['#00a65a', '#da0505', '#f39c12']
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
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>