<?php
require_once '../../includes/helpers.php'; // Include your helper functions
require_once '../models/sales.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$page_title = "Sales Dashboard";

$current_month = date('Y-m-01');
$targets = get_monthly_targets($current_user['id'], $current_month);
$sales = get_monthly_sales($current_user['id'], $current_month);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Dashboard</title>
    <?php include_once("../../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
          

          <div class="content-wrapper">
            <section class="content-header">
              <h1>Sales Performance Dashboard</h1>
            </section>

            <section class="content">
              <div class="row">
                <div class="col-md-6">
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h3>$<?= number_format($sales['total'], 2) ?></h3>
                      <p>Sales This Month</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="small-box bg-green">
                    <div class="inner">
                      <h3>$<?= number_format($targets['target_amount'], 2) ?></h3>
                      <p>Target This Month</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="box">
                <div class="box-header with-border"><h3 class="box-title">Performance Chart</h3></div>
                <div class="box-body">
                  <canvas id="targetChart"></canvas>
                </div>
              </div>
            </section>
          </div>
          

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
    <script src="/public/plugins/chart.js/Chart.min.js"></script>
    <script>
      const ctx = document.getElementById('targetChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Target', 'Actual'],
          datasets: [{
            label: 'Amount',
            data: [<?= $targets['target_amount'] ?>, <?= $sales['total'] ?>],
            backgroundColor: ['#00a65a', '#00c0ef']
          }]
        }
      });
    </script>
  </body>
  <!--end::Body-->
</html>
