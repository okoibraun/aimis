<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$empid = isset($_SESSION['employee_id']) ?? null;
// Get the most recent payroll period
$period_sql = "SELECT DISTINCT payment_date FROM payroll ORDER BY payment_date DESC LIMIT 1";
$period_result = $conn->query($period_sql);
$latest_period = ($period_result->num_rows > 0) ? $period_result->fetch_assoc()['pay_period'] : null;

if (!$latest_period) {
    echo "<div class='alert alert-warning'>No payroll data found.</div>";
    exit;
}

// Get payment data for latest period
$sql = "
SELECT 
    p.employee_id, 
    e.first_name, 
    e.last_name,
    e.bank_name,
    e.bank_account_number,
    p.net_salary 
FROM 
    payroll p
JOIN 
    employees e ON p.employee_id = e.id
WHERE 
    p.payment_date = '$latest_period' AND p.employee_id = '$empid' 
";

$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | View Leave</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
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
                <section class="content-header">
                    <h1>Bank Payment Instructions</h1>
                    <small>Payroll Period: <?= htmlspecialchars($latest_period) ?></small>
                </section>

                <section class="content">
                    <div class="card">
                    <div class="card-header">
                        <a href="export_bank_payment.php?pay_period=<?= urlencode($latest_period) ?>" class="btn btn-success btn-sm float-right">
                        <i class="fas fa-download"></i> Export CSV
                        </a>
                        <h3 class="card-title">Transfer Instructions</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <?php if ($result->num_rows > 0): ?>
                        <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                            <th>Employee</th>
                            <th>Bank</th>
                            <th>Account Number</th>
                            <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['bank_name']) ?></td>
                            <td><?= htmlspecialchars($row['bank_account_number']) ?></td>
                            <td><?= number_format($row['net_salary'], 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        </table>
                        <?php else: ?>
                        <div class="alert alert-info">No payment data available for the latest period.</div>
                        <?php endif; ?>
                    </div>
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
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
