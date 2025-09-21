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

$pending_orders = $conn->query("SELECT COUNT(id) AS total FROM sales_orders WHERE status = 'pending' AND company_id = $company_id AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())")->fetch_assoc()['total'];
$shipped_orders = $conn->query("SELECT COUNT(id) AS total FROM sales_orders WHERE status = 'shipped' AND company_id = $company_id AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())")->fetch_assoc()['total'];
$confirmed_orders = $conn->query("SELECT COUNT(id) AS total FROM sales_orders WHERE status = 'confirmed' AND company_id = $company_id AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())")->fetch_assoc()['total'];
$cancelled_orders = $conn->query("SELECT COUNT(id) AS total FROM sales_orders WHERE status = 'cancelled' AND company_id = $company_id AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())")->fetch_assoc()['total'];

// Fetch Customers / Leads (Latest)
$latest_customers = $conn->query("SELECT * FROM sales_customers WHERE customer_type = 'customer' AND company_id = $company_id ORDER BY created_at DESC LIMIT 8");
$latest_leads = $conn->query("SELECT * FROM sales_customers WHERE customer_type = 'lead' AND company_id = $company_id ORDER BY created_at DESC LIMIT 8");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM Dashboard</title>
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
                                <h1 class="m-0">CRM <small>Dashboard</small></h1>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-end mt-3">
                                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                                    <li class="breadcrumb-item active">CRM</li>
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
                            <div class="col-md-7">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Monthly Recap Report</h5>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <p class="text-center">
                                            <strong>Orders for: <?= date('F') ?>, <?= date('Y') ?></strong>
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
                                    <!-- ./card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-5">

                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">CRM Recap</h3>
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
                                                    <canvas id="pieChart" height="540" width="1077" style="display: block; height: 220px; width: 359px;" class="chartjs-render-monitor"></canvas>
                                                </div>
                                                <!-- ./chart-responsive -->
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-md-5">
                                                <ul class="chart-legend clearfix">
                                                <li><i class="far fa-circle text-success"></i> Orders</li>
                                                <li><i class="far fa-circle text-warning"></i> Customers</li>
                                                <li><i class="far fa-circle text-secondary"></i> Leads</li>
                                                <li><i class="far fa-circle text-info"></i> Activities</li>
                                                <li><i class="far fa-circle" style="color: #040342;"></i> Campaigns</li>
                                                <li><i class="far fa-circle" style="color: #700687;"></i> Segments</li>
                                                <li><i class="far fa-circle" style="color: #da0505;"></i> Reminders</li>
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
                                                <h3 class="card-title">Latest Customers</h3>
                                                <div class="card-tools">
                                                    <a href="/modules/crm/customers/" class="btn-link btn-sm">View All</a>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body p-0">
                                                <table class="table table-hover table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($latest_customers as $customer): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($customer['name']) ?></td>
                                                        <td><?= htmlspecialchars($customer['email']) ?></td>
                                                        <td><?= htmlspecialchars($customer['phone']) ?></td>
                                                        <td><?php $explode = explode(" ", $customer['created_at']); echo $explode[0] == date('Y-m-d') ? "Today" : $explode[0]; ?></td>
                                                        <td>
                                                        <a href="/modules/crm/customers/customer.php?id=<?= $customer['id'] ?>" class="text-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
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
                                                <h3 class="card-title">New Leads</h3>
                                                <div class="card-tools">
                                                    <a href="/modules/crm/leads/" class="btn-link btn-sm">View All</a>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body p-0">
                                                <table class="table table-hover table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Phone</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($latest_leads as $lead): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($lead['name']) ?></td>
                                                        <td><?= htmlspecialchars($lead['email']) ?></td>
                                                        <td><?= htmlspecialchars($lead['phone']) ?></td>
                                                        <td><?php $explode = explode(" ", $lead['created_at']); echo $explode[0] == date('Y-m-d') ? "Today" : $explode[0]; ?></td>
                                                        <td>
                                                        <a href="/modules/crm/leads/lead.php?id=<?= $lead['id'] ?>" class="text-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
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

                                <div class="row mb-3">
                                    <div class="col-7">
                                        <!-- TABLE: LATEST ORDERS -->
                                        <div class="card">
                                            <div class="card-header border-transparent">
                                                <h3 class="card-title">Latest Orders</h3>
                                                <div class="card-tools">
                                                    <a href="/modules/crm/orders/" class="btn btn-sm btn-info">View All Orders</a>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-striped m-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Order #</th>
                                                                <th>Customer</th>
                                                                <th>Amount</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($conn->query("SELECT * FROM sales_orders WHERE company_id = $company_id  LIMIT 10") as $order) { ?>
                                                            <tr>
                                                                <td><?= $order['order_number'] ?></td>
                                                                <td>
                                                                    <?php 
                                                                        $customer = $conn->query("SELECT * FROM sales_customers WHERE id = {$order['customer_id']} AND company_id = $company_id")->fetch_assoc();
                                                                        echo $customer['customer_type'] == 'lead' ? "{$customer['name'] (Lead)}" : $customer['name'];
                                                                    ?>
                                                                </td>
                                                                <td><?= $order['total_amount'] ?></td>
                                                                <td>
                                                                    <span class="text text-<?= 
                                                                    match($order['status']) {
                                                                    'pending' => 'info',
                                                                    'confirmed' => 'success',
                                                                    'shipped' => 'primary',
                                                                    'cancelled' => 'danger',
                                                                    default => 'warning'
                                                                    } ?>"><?= ucfirst($order['status']) ?></span>
                                                                </td>
                                                                <td>
                                                                    <a href="/modules/crm/orders/order.php?id=<?= $order['id'] ?>" class="text-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- /.table-responsive -->
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->
                                    </div>
                                    <div class="col-5">
                                        <div class="card">
                                            <div class="card-header">
                                                Campaigns
                                            </div>
                                            <div class="card-body p-0 table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Status</th>
                                                            <th>Schedule</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($conn->query("SELECT * FROM crm_campaigns WHERE company_id = $company_id ORDER BY scheduled_at DESC LIMIT 10") as $row): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($row['campaign_name']) ?></td>
                                                            <td><?= ucfirst($row['status']) ?></td>
                                                            <td><?= $row['scheduled_at'] ?? '—' ?></td>
                                                            <td>
                                                                <a href="edit.php?id=<?= $row['id'] ?>" class="text-info"><i class="fas fa-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-7">
                                        <!-- TABLE: LATEST Activities -->
                                        <div class="card">
                                            <div class="card-header border-transparent">
                                                <h3 class="card-title">Latest Activities</h3>
        
                                                <div class="card-tools">
                                                    <a href="/modules/crm/activities/" class="btn btn-sm btn-info">View All</a>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body p-0 table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Subject</th>
                                                        <th>Related To</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                        <th>Assigned</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($conn->query("SELECT * FROM crm_activities WHERE company_id = $company_id ORDER BY due_date DESC LIMIT 8") as $a): ?>
                                                        <tr>
                                                            <td><?= ucfirst($a['type']) ?></td>
                                                            <td><?= $a['subject'] ?></td>
                                                            <?php $related_to = $conn->query("SELECT name, title, customer_type FROM sales_customers WHERE id={$a['related_id']}")->fetch_assoc(); ?>
                                                            <td><?= ucfirst($a['related_type']) ?> : <?= !empty($a['related_id']) ? ($related_to['customer_type'] == 'customer' ? $related_to['name'] : $related_to['title']) : 'none'; ?></td>
                                                            <td><?= $a['due_date'] ?></td>
                                                            <td><?= ucfirst($a['status']) ?></td>
                                                            <td><?= $uname = !empty($a['assigned_to']) ? $conn->query("SELECT name FROM users WHERE id={$a['assigned_to']}")->fetch_assoc()['name'] : 'none'; ?></td>
                                                            <td>
                                                                <a href="edit.php?id=<?= $a['id'] ?>" class="text-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
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
                                    <div class="col"></div>
                                </div>
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
                        label: 'Pending ',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        data: [<?= $pending_orders > 0 ? $pending_orders : 0 ?>]
                    },
                    {
                        label: 'Shipped',
                        backgroundColor: 'green',
                        data: [<?= $shipped_orders > 0 ? $shipped_orders : 0 ?>]
                    },
                    {
                        label: 'Confirmed',
                        backgroundColor: 'blue',
                        data: [<?= $confirmed_orders > 0 ? $confirmed_orders : 0 ?>]
                    },
                    {
                        label: 'Cancelled',
                        backgroundColor: 'red',
                        data: [<?= $cancelled_orders > 0 ? $cancelled_orders : 0 ?>]
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
                'Orders',
                'Customers',
                'Leads',
                'Activities',
                'Campaigns',
                'Segments',
                'Remiders'
                ],
                datasets: [
                {
                    data: [
                        <?= $conn->query("SELECT COUNT(id) AS total FROM sales_orders WHERE company_id = $company_id")->fetch_assoc()['total']; ?>, 
                        <?= $conn->query("SELECT COUNT(id) AS total FROM sales_customers WHERE company_id = $company_id AND customer_type='customer'")->fetch_assoc()['total']; ?>, 
                        <?= $conn->query("SELECT COUNT(id) AS total FROM sales_customers WHERE company_id = $company_id AND customer_type='lead'")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM crm_activities WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM crm_campaigns WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM crm_segments WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM crm_reminders WHERE company_id = $company_id")->fetch_assoc()['total']; ?>],
                    backgroundColor: ['#00a65a', '#f39c12', '#6c757d', '#3c8dbc', '#040342', '#700687', '#da0505']
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
