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

$products_tbl = 'inventory_products' ?? 'sales_products';
$work_orders = $conn->query("SELECT id, order_code FROM production_work_orders ORDER BY id DESC");
$materials = $conn->query("SELECT id, name FROM {$products_tbl} WHERE is_raw_material = 1");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - QC</title>
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
                <section class="content-header"><h1>New QC Checkpoint</h1></section>
                <section class="content">
                    <form action="save.php" method="post">
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
                            <label>Checkpoint Type</label>
                            <select name="checkpoint_type" class="form-control" id="type-select" required>
                                <option value="Incoming">Incoming</option>
                                <option value="In-Process">In-Process</option>
                                <option value="Final">Final</option>
                            </select>
                        </div>

                        <div class="form-group" id="material-group">
                            <label>Material (Incoming only)</label>
                            <select name="material_id" class="form-control">
                                <option value="">Select</option>
                                <?php while($m = mysqli_fetch_assoc($materials)): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description / Process</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Result</label>
                            <select name="result" class="form-control" required>
                                <option value="Pass">Pass</option>
                                <option value="Fail">Fail</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>

                        <button type="submit" name="action" value="create" class="btn btn-success">Save</button>
                    </form>

                    <script>
                    document.getElementById('type-select').addEventListener('change', function () {
                        const materialGroup = document.getElementById('material-group');
                        materialGroup.style.display = this.value === 'Incoming' ? 'block' : 'none';
                    });
                    </script>
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
