<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$employee_id = $_GET['id'];

$query = "SELECT s.*, e.first_name, e.last_name 
          FROM salary_structures s 
          JOIN employees e ON s.employee_id = e.id 
          WHERE s.employee_id = $employee_id 
          ORDER BY s.effective_from DESC LIMIT 1";
$res = $conn->query($query);
$structure = $res->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memos Dashboard</title>
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

            <h3>Salary Structure</h3>

            <a href="setup_structure.php" class="btn btn-primary float-end">Create new Salary Structure</a>
            <?php if ($structure): ?>
                <p><strong>Employee:</strong> <?= $structure['first_name'] . ' ' . $structure['last_name'] ?></p>
                <p><strong>Basic Salary:</strong> <?= number_format($structure['basic_salary'], 2) ?> <?= $structure['currency'] ?></p>
                <p><strong>Housing Allowance:</strong> <?= number_format($structure['housing_allowance'], 2) ?></p>
                <p><strong>Transport Allowance:</strong> <?= number_format($structure['transport_allowance'], 2) ?></p>
                <p><strong>Other Allowances:</strong> <?= number_format($structure['other_allowances'], 2) ?></p>
                <p><strong>Deductions:</strong> <?= number_format($structure['deductions'], 2) ?></p>
                <p><strong>Effective From:</strong> <?= $structure['effective_from'] ?></p>
            <?php else: ?>
                <div class="alert alert-warning">No salary structure assigned yet.</div>
            <?php endif; ?>

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
