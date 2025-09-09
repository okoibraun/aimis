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
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$products_tbl = 'inventory_products' ?? 'sales_products';
$products = $conn->query("SELECT id, name FROM $products_tbl");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - BOM</title>
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
                <section class="content-header"><h1>Create BOM</h1></section>
                <section class="content">
                    <form action="save.php" method="post">
                        <div class="form-group">
                            <label>Product</label>
                            <select name="product_id" class="form-control" required>
                                <option value="">Select Product</option>
                                <?php while($p = mysqli_fetch_assoc($products)): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Version</label>
                            <input type="text" name="version" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>

                        <!-- Inside the <form> tag, after BOM fields -->
                        <h4 class="mt-3">Materials</h4>
                        <table class="mb-3" id="materials-table">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Qty</th>
                                    <th>UOM</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary" onclick="addRow()">+ Add Material</button>

                        <script>
                            function addRow() {
                                const row = `<tr>
                                    <td>
                                        <select name="material_id[]" class="form-control" required>
                                            <option value="">Select</option>
                                            <?php
                                            $materials = mysqli_query($conn, "SELECT id, name FROM inventory_products WHERE is_raw_material=1");
                                            while($m = mysqli_fetch_assoc($materials)):
                                            ?>
                                                <option value="<?= $m['id'] ?>"><?= $m['name'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" name="material_qty[]" class="form-control" step="0.01" required></td>
                                    <td><input type="text" name="material_uom[]" class="form-control" required></td>
                                    <td><button type="button" onclick="this.closest('tr').remove()" class="btn btn-danger btn-sm">Remove</button></td>
                                </tr>`;
                                document.querySelector('#materials-table tbody').insertAdjacentHTML('beforeend', row);
                            }
                        </script>

                        
                        <button type="submit" name="action" value="create" class="btn btn-success">Save BOM</button>
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
