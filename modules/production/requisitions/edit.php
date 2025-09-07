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

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'];
$req = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM production_requisitions WHERE id = $id"));
$items = mysqli_query($conn, "SELECT * FROM production_requisition_items WHERE requisition_id = $id");

$work_orders = mysqli_query($conn, "SELECT id, order_code FROM production_work_orders");
$materials = mysqli_query($conn, "SELECT id, name FROM inventory_products WHERE is_raw_material = 1");
$materials_arr = [];
while ($m = mysqli_fetch_assoc($materials)) $materials_arr[$m['id']] = $m['name'];
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Requisitions</title>
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
                <section class="content-header"><h1>Edit Requisition</h1></section>
                <section class="content">
                    <form action="save.php" method="post">
                        <input type="hidden" name="id" value="<?= $req['id'] ?>">
                        <div class="form-group">
                            <label>Requisition Code</label>
                            <input type="text" name="requisition_code" class="form-control" value="<?= $req['requisition_code'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Work Order</label>
                            <select name="work_order_id" class="form-control" required>
                                <?php while($w = mysqli_fetch_assoc($work_orders)): ?>
                                    <option value="<?= $w['id'] ?>" <?= $w['id'] == $req['work_order_id'] ? 'selected' : '' ?>>
                                        <?= $w['order_code'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <h4>Materials</h4>
                        <table class="table" id="material-table">
                            <thead><tr><th>Material</th><th>Requested</th><th>Issued</th><th>Consumed</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($items)): ?>
                                <tr>
                                    <td>
                                        <select name="material_id[]" class="form-control">
                                            <?php foreach ($materials_arr as $mid => $mname): ?>
                                                <option value="<?= $mid ?>" <?= $mid == $item['material_id'] ? 'selected' : '' ?>><?= $mname ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" name="qty_requested[]" step="0.01" class="form-control" value="<?= $item['qty_requested'] ?>"></td>
                                    <td><input type="number" name="qty_issued[]" step="0.01" class="form-control" value="<?= $item['qty_issued'] ?>"></td>
                                    <td><input type="number" name="qty_consumed[]" step="0.01" class="form-control" value="<?= $item['qty_consumed'] ?>"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary" onclick="addRow()">+ Add Material</button>

                        <script>
                        function addRow() {
                            const row = `<tr>
                                <td>
                                    <select name="material_id[]" class="form-control">
                                        <?php foreach ($materials_arr as $mid => $mname): ?>
                                            <option value="<?= $mid ?>"><?= $mname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="qty_requested[]" step="0.01" class="form-control"></td>
                                <td><input type="number" name="qty_issued[]" step="0.01" class="form-control"></td>
                                <td><input type="number" name="qty_consumed[]" step="0.01" class="form-control"></td>
                                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
                            </tr>`;
                            document.querySelector('#material-table tbody').insertAdjacentHTML('beforeend', row);
                        }
                        </script>

                        <button type="submit" name="action" value="update" class="btn btn-success">Update</button>
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
