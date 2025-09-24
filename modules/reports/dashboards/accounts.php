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

$revenue = $conn->query("SELECT SUM(total_amount) AS revenue FROM sales_invoices WHERE company_id = $company_id AND MONTH(due_date) = MONTH(CURDATE()) AND YEAR(due_date) = YEAR(CURDATE())")->fetch_assoc()['revenue'];
$profit = $conn->query("SELECT SUM(total_amount) AS profit FROM sales_invoices WHERE company_id = $company_id AND status='paid' AND payment_status='paid' AND MONTH(due_date) = MONTH(CURDATE())
            AND YEAR(due_date) = YEAR(CURDATE())")->fetch_assoc()['profit'];
$vat_tax_amount = $conn->query("SELECT SUM(vat_tax_amount) AS vat_tax_amount FROM sales_invoices WHERE company_id = $company_id AND status='paid' AND payment_status='paid' AND MONTH(due_date) = MONTH(CURDATE())
            AND YEAR(due_date) = YEAR(CURDATE())")->fetch_assoc()['vat_tax_amount'];
$wht_tax_amount = $conn->query("SELECT SUM(wht_tax_amount) AS wht_tax_amount FROM sales_invoices WHERE company_id = $company_id AND status='paid' AND payment_status='paid' AND MONTH(due_date) = MONTH(CURDATE())
            AND YEAR(due_date) = YEAR(CURDATE())")->fetch_assoc()['wht_tax_amount'];
$profit -= $vat_tax_amount;
$profit -= $wht_tax_amount;

$vendor_bills = $conn->query("SELECT SUM(amount) AS total_amount, SUM(paid_amount) AS total_paid 
    FROM bills
    WHERE company_id = $company_id AND MONTH(bill_date) = MONTH(CURDATE())
            AND YEAR(bill_date) = YEAR(CURDATE())
")->fetch_assoc();

// echo $vendor_bills['total_amount'];
// echo $vendor_bills['total_paid'];
// exit;

// $budgets = $conn->query("SELECT * FROM budgets WHERE company_id = $company_id AND MONTH(due_date) = MONTH(CURDATE()) AND YEAR(due_date) = YEAR(CURDATE())");

// $ledgers = $conn->query();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts Reports</title>
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
                                <h1 class="m-0">Accounts</h1>
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
                        <!-- Info Box -->
                        <div class="row">
                            <div class="col-md-3">
                                <!-- Info Boxes Style 2 -->
                                <div class="info-box mb-3 bg-info text-white">
                                    <span class="info-box-icon text-white" style="border: 1px solid #fff;">
                                        <i class="fas fa-book"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Charts of Accounts</span>
                                        <span class="info-box-number">
                                            <?= $conn->query("SELECT COUNT(id) AS total FROM accounts WHERE company_id = $company_id")->fetch_assoc()['total'] ?>
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="col-md-3">
                                <div class="info-box mb-3 bg-success text-white">
                                    <span class="info-box-icon text-white" style="border: 1px solid #fff;">
                                        <i class="fas fa-book-open"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Journal Entries</span>
                                        <span class="info-box-number"><?= $conn->query("SELECT COUNT(id) AS total FROM journal_entries WHERE company_id = $company_id")->fetch_assoc()['total'] ?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="col-md-3">
                                <div class="info-box mb-3 bg-primary text-white">
                                    <span class="info-box-icon text-white" style="border: 1px solid #fff;">
                                        <i class="fas fa-user-tie"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Vendors</span>
                                        <span class="info-box-number"><?= $conn->query("SELECT COUNT(id) AS total FROM accounts_vendors WHERE company_id = $company_id")->fetch_assoc()['total'] ?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            <div class="col-md-3">
                                <div class="info-box mb-3 text-white bg-danger">
                                    <span class="info-box-icon text-white" style="border: 1px solid #fff;">
                                        <i class="fas fa-calendar-check"></i>
                                    </span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Accruals</span>
                                        <span class="info-box-number"><?= $conn->query("SELECT COUNT(id) AS total FROM accruals WHERE company_id = $company_id")->fetch_assoc()['total'] ?></span>
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
                                        <h5>Financial Summary</h5>
                                        <div class="chart">
                                            <canvas id="receivablesChart" height="540" style="height: 180px; display: block; width: 359px;" width="1077" class="chartjs-render-monitor"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card card-success">
                                    <div class="card-body">
                                        <h5 class="text-center">
                                             Vendor Bills Summary
                                        </h5>

                                        <div class="chart">
                                            <!-- Sales Chart Canvas -->
                                            <canvas id="billsChart" height="540" style="height: 260px; display: block; width: 359px;" width="1077" class="chartjs-render-monitor"></canvas>
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
                            <!-- Latest Budgets -->
                            <div class="col-md-7">
                                <div class="card">
                                    <?php
                                        $budgets = $conn->query("SELECT * FROM budgets WHERE company_id = $company_id ORDER BY start_date DESC LIMIT 8");
                                    ?>
                                    <div class="card-header">
                                        <h3 class="card-title">Latest Budgets</h3>
                                        <div class="card-tools">
                                            <a href="/accounts/budgeting/" class="btn btn-info btn-sm">View All</a>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                <th>Budget Name</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Total Amount</th>
                                                <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($budgets as $budget): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($budget['budget_name']) ?></td>
                                                    <td><?= $budget['start_date'] ?></td>
                                                    <td><?= $budget['end_date'] ?></td>
                                                    <td>N<?= number_format($budget['total_amount'], 2) ?></td>
                                                    <td>
                                                    <a href="budget?id=<?= $budget['id']; ?>" class="text-info"><i class="fas fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- /.col -->

                            <!-- Accounts Recap -->
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Accounts Recap</h3>
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
                                                    <li><i class="far fa-circle text-success"></i> Charts of Accounts</li>
                                                    <li><i class="far fa-circle text-warning"></i> Journal Entries</li>
                                                    <li><i class="far fa-circle text-info"></i> Vendors</li>
                                                    <li><i class="far fa-circle" style="color: #700687;"></i> Payables</li>
                                                    <li><i class="far fa-circle" style="color: #da0505;"></i> Budgets</li>
                                                    <li><i class="far fa-circle" style="color: #ff00ff;"></i> Accruals</li>
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
                            <div class="col-md-6">
                                <!-- CUSTOMERS LIST -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Latest Journal Entries</h3>
                                        <div class="card-tools">
                                            <a href="/accounts/journal_entries/" class="btn btn-info btn-sm">View All</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Total Debit</th>
                                                    <th>Total Credit</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(in_array($_SESSION['user_role'], super_roles())) {
                                                    $query = "SELECT je.*, 
                                                            (SELECT SUM(debit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_debit,
                                                            (SELECT SUM(credit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_credit
                                                            FROM journal_entries je WHERE je.company_id = $company_id ORDER BY entry_date DESC";
                                                } else {
                                                    $query = "SELECT je.*, 
                                                            (SELECT SUM(debit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_debit,
                                                            (SELECT SUM(credit) FROM journal_lines WHERE journal_entry_id = je.id) AS total_credit
                                                            FROM journal_entries je WHERE je.company_id = $company_id AND je.user_id = $user_id OR je.employee_id = $employee_id ORDER BY entry_date DESC";
                                                }
                                                $result = $conn->query($query);

                                                foreach($result as $row) {
                                                ?>
                                                <tr>
                                                    <td><?= $row['entry_date'] ?></td>
                                                    <td><?= $row['description'] ?></td>
                                                    <td><?= $row['total_debit'] ?></td>
                                                    <td><?= $row['total_credit'] ?></td>
                                                    <td>
                                                        <a href='edit?id=<?= $row['id'] ?>' class='text-info'><i class="fas fa-eye"></i></a>
                                                        <a href='delete?id=<?= $row['id'] ?>' class='text-danger' onclick='return confirm("Delete this entry?")'><i class="fas fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        <!-- /.journal entries -->
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
                                        <h3 class="card-title">New Vendors</h3>
                                        <div class="card-tools">
                                            <a href="/modules/production/resources/" class="btn btn-info btn-sm">View All</a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0 table-responsive">
                                        <?php $vendors = $conn->query("SELECT * FROM accounts_vendors WHERE company_id = $company_id LIMIT 8"); ?>
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Vendor Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                    <th>Country</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($vendors as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                                    <td><?= htmlspecialchars($row['country']) ?></td>
                                                    <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
                                                    <td>
                                                    <a href="edit?id=<?= $row['id'] ?>" class="text-primary"><i class="fas fa-edit"></i></a>
                                                    <a href="vendor?id=<?= $row['id']; ?>" class="text-info"><i class="fas fa-eye"></i></a>
                                                    <form action="vendorsController" method="POST" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <button class="text-danger" onclick="return confirm('Delete this Vendor?')"><i class="fas fa-trash"></i></button>
                                                    </form>
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
                            <!-- Left col -->
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
            // - MONTHLY RECEIVABLES CHART -
            //-----------------------

            // Get context with jQuery - using jQuery's .get() method.
            var receivablesChartCanvas = $('#receivablesChart').get(0).getContext('2d')

            var receivablesChartData = {
                labels: ['<?= date('F') ?>'],
                datasets: [
                    {
                        label: 'Revenue',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        data: [<?= $revenue ?>]
                    },
                    {
                        label: 'Profit',
                        backgroundColor: 'green',
                        data: [<?= $profit ?>]
                    },
                    {
                        label: 'VAT Tax',
                        backgroundColor: 'red',
                        data: [<?= $vat_tax_amount ?>]
                    },
                    {
                        label: 'WHT Tax',
                        backgroundColor: 'yellow',
                        data: [<?= $wht_tax_amount ?>]
                    }
                ]
            }

            var receivablesChartOptions = {
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
            var receivablesChart = new Chart(receivablesChartCanvas, {
                type: 'bar',
                data: receivablesChartData,
                options: receivablesChartOptions
            });

            //---------------------------
            // - END MONTHLY RECEIVABLES CHART-
            //---------------------------

            //-----------------------
            // - MONTHLY RECEIVABLES CHART -
            //-----------------------

            // Get context with jQuery - using jQuery's .get() method.
            var billsChartCanvas = $('#billsChart').get(0).getContext('2d')

            var billsChartData = {
                labels: ['<?= date('F') ?>'],
                datasets: [
                    {
                        label: 'Total Amount',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        data: [<?= $vendor_bills['total_amount'] ?>]
                    },
                    {
                        label: 'Total Aamount Paid',
                        backgroundColor: 'green',
                        data: [<?= $vendor_bills['total_paid'] ?>]
                    }
                ]
            }

            var billsChartOptions = {
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
            var billsChart = new Chart(billsChartCanvas, {
                type: 'bar',
                data: billsChartData,
                options: billsChartOptions
            });

            //---------------------------
            // - END MONTHLY RECEIVABLES CHART-
            //---------------------------

            //-------------
            // - PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieData = {
                labels: [
                'Chart of Accounts',
                'Journal Entries',
                'Vendors',
                'Payables',
                'Budgets',
                'Accruals'
                ],
                datasets: [
                {
                    data: [
                        <?= $conn->query("SELECT COUNT(id) AS total FROM accounts WHERE company_id = $company_id")->fetch_assoc()['total']; ?>, 
                        <?= $conn->query("SELECT COUNT(id) AS total FROM journal_entries WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM accounts_vendors WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM bills WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM budgets WHERE company_id = $company_id")->fetch_assoc()['total']; ?>,
                        <?= $conn->query("SELECT COUNT(id) AS total FROM production_downtime_logs WHERE company_id = $company_id")->fetch_assoc()['total']; ?>
                    ],
                    backgroundColor: ['#00a65a', '#f39c12', '#3c8dbc', '#700687', '#da0505', '#ff00ff']
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
