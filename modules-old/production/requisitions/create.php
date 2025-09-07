<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$products_tbl = "inventory_products" ?? "sales_products";
$work_orders = mysqli_query($conn, "SELECT id, order_code FROM production_work_orders");
$materials = mysqli_query($conn, "SELECT id, name FROM $products_tbl WHERE is_raw_material = 1");
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
                <section class="content-header">
                    <h1>New Material Requisition</h1>
                </section>
                <section class="content">
                    <form action="save.php" method="post">
                        <div class="form-group">
                            <label>Requisition Code</label>
                            <input type="text" name="requisition_code" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Work Order</label>
                            <select name="work_order_id" class="form-control" required>
                                <option value="">Select</option>
                                <?php while($w = mysqli_fetch_assoc($work_orders)): ?>
                                    <option value="<?= $w['id'] ?>"><?= $w['order_code'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <h4>Materials Requested</h4>
                        <table class="table" id="material-table">
                            <thead><tr><th>Material</th><th>Requested Qty</th><th>Issued Qty</th><th>Consumed Qty</th><th>Action</th></tr></thead>
                            <tbody></tbody>
                        </table>
                        <button type="button" class="btn btn-secondary" onclick="addRow()">+ Add Material</button>

                        <script>
                        function addRow() {
                            const row = `<tr>
                                <td>
                                    <select name="material_id[]" class="form-control" required>
                                        <option value="">Select</option>
                                        <?php mysqli_data_seek($materials, 0); while($m = mysqli_fetch_assoc($materials)): ?>
                                            <option value="<?= $m['id'] ?>"><?= $m['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" name="qty_requested[]" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="qty_issued[]" class="form-control"></td>
                                <td><input type="number" step="0.01" name="qty_consumed[]" class="form-control"></td>
                                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Remove</button></td>
                            </tr>`;
                            document.querySelector('#material-table tbody').insertAdjacentHTML('beforeend', row);
                        }
                        </script>

                        <button type="submit" name="action" value="create" class="btn btn-success">Save Requisition</button>
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
