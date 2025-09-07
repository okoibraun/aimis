<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$work_order_id = $_GET['work_order_id'];
$resources = mysqli_query($conn, "SELECT * FROM production_resources ORDER BY type, name");
$assigned = mysqli_query($conn, "SELECT * FROM production_work_order_resources WHERE work_order_id = $work_order_id");
$assigned_map = [];
while ($row = mysqli_fetch_assoc($assigned)) {
    $assigned_map[$row['resource_id']] = $row['assigned_hours'];
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Resources</title>
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
                <section class="content-header"><h1>Assign Resources</h1></section>
                <section class="content">
                    <form action="save.php" method="post">
                        <input type="hidden" name="work_order_id" value="<?= $work_order_id ?>">
                        <table class="table table-bordered">
                            <thead><tr><th>Resource</th><th>Assigned Hours</th></tr></thead>
                            <tbody>
                                <?php while ($r = mysqli_fetch_assoc($resources)): ?>
                                    <tr>
                                        <td>
                                            <label>
                                                <input type="checkbox" name="resource_id[]" value="<?= $r['id'] ?>"
                                                    <?= isset($assigned_map[$r['id']]) ? 'checked' : '' ?>>
                                                <?= $r['name'] ?> (<?= $r['type'] ?>)
                                            </label>
                                        </td>
                                        <td>
                                            <input type="number" step="0.1" name="assigned_hours[<?= $r['id'] ?>]"
                                                class="form-control"
                                                value="<?= $assigned_map[$r['id']] ?? '' ?>">
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <button type="submit" name="action" value="assign" class="btn btn-success">Assign Resources</button>
                    </form>
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
