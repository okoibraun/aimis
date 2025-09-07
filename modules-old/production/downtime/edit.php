<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}


$id = $_GET['id'];
$log = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM production_downtime_logs WHERE id = $id"));
$work_orders = mysqli_query($conn, "SELECT id, order_code FROM production_work_orders");
$resources = mysqli_query($conn, "SELECT id, name FROM production_resources");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Downtime</title>
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
                <section class="content-header"><h1>Edit Downtime Log</h1></section>
                <section class="content">
                    <form action="save.php" method="post">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $log['id'] ?>">

                        <div class="form-group">
                            <label>Work Order</label>
                            <select name="work_order_id" class="form-control" required>
                                <?php while($w = mysqli_fetch_assoc($work_orders)): ?>
                                    <option value="<?= $w['id'] ?>" <?= $w['id'] == $log['work_order_id'] ? 'selected' : '' ?>>
                                        <?= $w['order_code'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Resource</label>
                            <select name="resource_id" class="form-control">
                                <option value="">None</option>
                                <?php while($r = mysqli_fetch_assoc($resources)): ?>
                                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $log['resource_id'] ? 'selected' : '' ?>>
                                        <?= $r['name'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" name="downtime_reason" class="form-control" value="<?= $log['downtime_reason'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="datetime-local" name="start_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($log['start_time'])) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>End Time</label>
                            <input type="datetime-local" name="end_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($log['end_time'])) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control"><?= $log['remarks'] ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
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
