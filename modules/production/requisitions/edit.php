<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'];
$req = $conn->query("SELECT * FROM production_requisitions WHERE id = $id AND company_id = $company_id")->fetch_assoc();
$items = $conn->query("SELECT * FROM production_requisition_items WHERE requisition_id = $id AND company_id = $company_id");

$work_orders = $conn->query("SELECT id, order_code FROM production_work_orders WHERE company_id = $company_id");
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
                <section class="content-header mt-3 mb-3">
                    <h1>Edit Requisition</h1>
                </section>

                <section class="content">
                    <form action="save.php" method="post" class="card">
                        <div class="card-body">
                            <input type="hidden" name="id" value="<?= $req['id'] ?>">
                            <div class="form-group">
                                <label>Requisition Code</label>
                                <input type="text" name="requisition_code" class="form-control" value="<?= $req['requisition_code'] ?>" required readonly>
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
                            
                            <div class="card mt-3 mb-3">
                                <div class="card-header">
                                    <h3 class="card-title">Materials</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="addRow()">+ Add Material</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table" id="material-table">
                                        <thead><tr><th>Material</th><th>Requested</th><th>Issued</th><th>Consumed</th><th>Action</th></tr></thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td><input type="text" name="material[]" class="form-control" value="<?= $item['material'] ?>" required></td>
                                                <td><input type="number" name="qty_requested[]" step="0.01" class="form-control" value="<?= $item['qty_requested'] ?>"></td>
                                                <td><input type="number" name="qty_issued[]" step="0.01" class="form-control" value="<?= $item['qty_issued'] ?>" readonly></td>
                                                <td><input type="number" name="qty_consumed[]" step="0.01" class="form-control" value="<?= $item['qty_consumed'] ?>"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <script>
                                        function addRow() {
                                            const row = `<tr>
                                                <td><input type="text" name="material[]" class="form-control" required></td>
                                                <td><input type="number" name="qty_requested[]" step="0.01" class="form-control"></td>
                                                <td><input type="number" name="qty_issued[]" step="0.01" class="form-control" readonly></td>
                                                <td><input type="number" name="qty_consumed[]" step="0.01" class="form-control"></td>
                                                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
                                            </tr>`;
                                            document.querySelector('#material-table tbody').insertAdjacentHTML('beforeend', row);
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="form-group float-end">
                                <a href="./" class="btn btn-default">Cancel</a>
                                <button type="submit" name="action" value="update" class="btn btn-success">Update</button>
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
