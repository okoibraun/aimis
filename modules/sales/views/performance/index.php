<?php
require_once '../../includes/helpers.php'; // Include your helper functions
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$page_title = "Sales Targets and Performance";

$month = $_GET['month'] ?? date('Y-m');
if(in_array($_SESSION['user_role'], system_users())) {
  $targets = $db->query("
      SELECT t.*, u.name, (
        SELECT SUM(total_amount) FROM sales_invoices
        WHERE user_id = t.user_id AND DATE_FORMAT(invoice_date, '%Y-%m') = $month
      ) AS actual_sales
      FROM sales_targets t
      JOIN users u ON u.id = t.user_id
      WHERE t.target_month = $month");
} else {
  $targets = $db->query("
      SELECT t.*, u.name, (
        SELECT SUM(total_amount) FROM sales_invoices
        WHERE user_id = t.user_id AND DATE_FORMAT(invoice_date, '%Y-%m') = $month
      ) AS actual_sales
      FROM sales_targets t
      JOIN users u ON u.id = t.user_id
      WHERE t.target_month = $month AND t.company_id = $company_id");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Targets</title>
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
                <h1>Sales Performance - <?= htmlspecialchars($month) ?></h1>
            </section>
            <section class="content">
                

                <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">
                        Sales Performace Targets
                      </h3>
                      <div class="card-tools">
                        <div class="row">
                          <div class="col">
                            <form method="get" class="">
                                <input type="month" class="" name="month" value="<?= $month ?>" />
                                <button class="btn btn-primary btn-sm">Filter</button>
                            </form>
                          </div>
                          <div class="col-auto">
                            <a href="set_target" class="btn btn-success btn-sm">Set Target</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <table class="table table-bordered">
                          <tr>
                            <th>Salesperson</th>
                            <th>Target</th>
                            <th>Actual</th>
                            <th>% Achieved</th>
                          </tr>
                          <?php foreach ($targets as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>$<?= number_format($row['target_amount'], 2) ?></td>
                            <td>$<?= number_format($row['actual_sales'] ?? 0, 2) ?></td>
                            <td><?= $row['target_amount'] > 0 ? round(($row['actual_sales'] / $row['target_amount']) * 100, 1) : 0 ?>%</td>
                          </tr>
                          <?php endforeach; ?>
                      </table>
                    </div>
                </div>
                
                <canvas id="performanceChart"></canvas>
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
      const chart = new Chart(document.getElementById('performanceChart'), {
        type: 'bar',
        data: {
          labels: <?= json_encode(array_column($targets, 'name')) ?>,
          datasets: [{
            label: 'Target',
            data: <?= json_encode(array_column($targets, 'target_amount')) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)'
          }, {
            label: 'Actual',
            data: <?= json_encode(array_column($targets, 'actual_sales')) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.6)'
          }]
        }
      });
    </script>

  </body>
  <!--end::Body-->
</html>
