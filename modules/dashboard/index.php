<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

require_once '../../config/config.php';
//require '../../functions/auth_functions.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/log_functions.php';
require_once '../../functions/subscription_functions.php';

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
<?php

// ensure_logged_in();

// Get user info
// $user = get_current_user_data();
$is_superadmin = $_SESSION['user_role'] ?? false;

// Count data
$total_companies = $conn->query("SELECT COUNT(id) AS total FROM companies WHERE id = $company_id")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(id) AS total FROM users WHERE company_id = $company_id")->fetch_assoc()['total'];
$total_logs = 20;
$subscription_status = 1;
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts Dashboard</title>
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
                <section class="content-header mt-3 mb-3">
                    <h1>Dashboard <small>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></small></h1>
                </section>

                <section class="content">
                    <div class="row">
                        <!-- Companies -->
                        <?php if (in_array($_SESSION['role'], super_roles())): ?>
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3><?= $total_companies ?></h3>
                                    <p><?= $is_superadmin == 'superadmin' ? 'Total Companies' : 'Your Company' ?></p>
                                </div>
                                <div class="small-box-icon">
                                    <i class="fa fa-building"></i>
                                </div>
                                <a href="../company/list.php" class="small-box-footer">
                                    Manage <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Users -->
                        <?php if (in_array($_SESSION['role'], super_roles())): ?>
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3><?= $total_users ?></h3>
                                    <p>Users</p>
                                </div>
                                <div class="small-box-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <a href="../user/list.php" class="small-box-footer">
                                    Manage <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Logs -->
                        <?php if (in_array($_SESSION['role'], super_roles())): ?>
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3><?= $total_logs ?></h3>
                                    <p>Recent Logs</p>
                                    <div class="small-box-icon">
                                        <i class="fa fa-list-alt"></i>
                                    </div>
                                </div>
                                <a href="../logs/activity.php" class="small-box-footer">
                                    View <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Subscriptions -->
                        <?php if (in_array($_SESSION['role'], super_roles())): ?>
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3><?= htmlspecialchars($subscription_status) ?></h3>
                                    <p>Subscription</p>
                                </div>
                                <div class="small-box-icon">
                                    <i class="fa fa-credit-card"></i>
                                </div>
                                <a href="../subscriptions/billing.php" class="small-box-footer">
                                    Manage <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
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
