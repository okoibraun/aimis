<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$company_id = $_SESSION['company_id'];

$sql = "SELECT c.*, e.* 
        FROM contracts c 
        JOIN employees e ON c.employee_id = e.id 
        WHERE c.company_id = $company_id 
        ORDER BY c.start_date DESC";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Contracts</title>
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
                <section class="content-header mt-4 mb-4">
                    <h3>Contracts</h3>
                </section>
                <section class="content">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Employees Contract</h3>
                            <div class="card-tools">
                                <a href="manage_contract.php" class="btn btn-primary">Manage Contract</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped DataTable">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Position</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Contract Type</th>
                                        <th>Salary</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $row): ?>
                                    <tr>
                                        <td><?= $row['employee_code'] . ' - ' . $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                        <td><?= $row['position'] ?></td>
                                        <td><?= $row['start_date'] ?></td>
                                        <td><?= $row['end_date'] ?></td>
                                        <td><?= $row['contract_type'] ?></td>
                                        <td><?= number_format($row['salary'], 2) ?></td>
                                        <td><?= $row['status'] ?></td>
                                        <td>
                                            <a href="view_contract.php?employee_id=<?= $row['id']; ?>" class="btn btn-info">View</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
