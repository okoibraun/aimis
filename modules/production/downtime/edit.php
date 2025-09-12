<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
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
                <section class="content-header mt-3 mb-3">
                    <h1>Edit Downtime Log</h1>
                </section>

                <section class="content">
                    <form action="save.php" method="post" class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Downtime Log Details
                            </h3>
                            <div class="card-tools">
                                <a href="./" class="btn btn-danger btn-sm">X</a>
                            </div>
                        </div>
                        <div class="card-body">
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
    
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <input type="datetime-local" name="start_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($log['start_time'])) ?>" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <input type="datetime-local" name="end_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($log['end_time'])) ?>" required>
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control"><?= $log['remarks'] ?></textarea>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="form-group float-end">
                                <a href="./" class="btn btn-default">Cancel</a>
                                <button type="submit" name="action" value="update" class="btn btn-success">Update Log</button>
                            </div>
                        </div>
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
