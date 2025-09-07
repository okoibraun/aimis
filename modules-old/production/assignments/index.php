<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$assignments = $conn->query("
    SELECT pra.*, pwo.order_code, pr.name AS resource_name
    FROM production_resource_assignments pra
    JOIN production_work_orders pwo ON pra.work_order_id = pwo.id
    JOIN production_resources pr ON pra.resource_id = pr.id
    ORDER BY pra.assigned_start DESC
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Assignments</title>
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

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>Assigned Resources</h1>
                    <a href="create.php" class="btn btn-success">New Assignment</a>
                </section>

                <section class="content">
                    <table class="table table-bordered">
                        <thead><tr>
                            <th>Work Order</th>
                            <th>Resource</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Shift</th>
                            <th>Remarks</th>
                        </tr></thead>
                        <tbody>
                            <?php while($a = mysqli_fetch_assoc($assignments)): ?>
                                <tr>
                                    <td><?= $a['order_code'] ?></td>
                                    <td><?= $a['resource_name'] ?></td>
                                    <td><?= $a['assigned_start'] ?></td>
                                    <td><?= $a['assigned_end'] ?></td>
                                    <td><?= $a['shift'] ?></td>
                                    <td><?= $a['remarks'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>
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
