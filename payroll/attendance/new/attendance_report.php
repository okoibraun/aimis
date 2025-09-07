<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Attendance Report</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

        <div class="container mt-4">
            <h3>Attendance Report</h3>

            <form method="GET" class="form-inline mb-3">
                <input type="date" name="date" class="form-control mr-2" value="<?= $_GET['date'] ?? '' ?>">
                <button type="submit" class="btn btn-info">Filter</button>
            </form>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $filter_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
                    // $query = "SELECT a.*, e.first_name, e.last_name 
                    //           FROM attendance_records a
                    //           JOIN employees e ON a.employee_id = e.id
                    //           WHERE attendance_date = '$filter_date'
                    //           ORDER BY e.last_name";
                    $query = "SELECT a.*, e.first_name, e.last_name 
                              FROM attendance a
                              JOIN employees e ON a.employee_id = e.id
                              WHERE date = '$filter_date'
                              ORDER BY e.last_name";
                    $res = $conn->query($query);
                    while ($row = $res->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $row['date'] ?></td>
                            <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                            <td><?= ucfirst($row['status']) ?></td>
                            <td><?= $row['remarks'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
