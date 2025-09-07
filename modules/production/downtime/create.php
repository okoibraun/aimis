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
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}


$work_orders = $conn->query("SELECT id, order_code FROM production_work_orders ORDER BY id DESC");
$resources = $conn->query("SELECT id, name FROM production_resources ORDER BY name");
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
                <section class="content-header"><h1>Log Downtime</h1></section>
                <section class="content">
                    <form action="save.php" method="post">
                        <input type="hidden" name="action" value="create">

                        <div class="form-group">
                            <label>Work Order</label>
                            <select name="work_order_id" class="form-control" required>
                                <option value="">Select</option>
                                <?php while($w = mysqli_fetch_assoc($work_orders)): ?>
                                    <option value="<?= $w['id'] ?>"><?= $w['order_code'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Resource (optional)</label>
                            <select name="resource_id" class="form-control">
                                <option value="">None</option>
                                <?php while($r = mysqli_fetch_assoc($resources)): ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" name="downtime_reason" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="datetime-local" name="start_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>End Time</label>
                            <input type="datetime-local" name="end_time" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Save Log</button>
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
