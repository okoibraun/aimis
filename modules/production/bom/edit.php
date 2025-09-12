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
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'];
$bom = $conn->query("SELECT * FROM production_bom WHERE id=$id AND company_id = $company_id")->fetch_assoc();
$products = $conn->query("SELECT id, name FROM sales_products WHERE company_id = $company_id");

$bom_items = $conn->query("SELECT * FROM production_bom_items WHERE bom_id = $id AND company_id = $company_id");

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
                <section class="content-header mt-3 mb-3">
                    <h1>Edit BOM</h1>
                </section>

                <section class="content">
                    <form action="save.php" method="post" class="card">
                        <div class="card-body">
                            <input type="hidden" name="id" value="<?= $bom['id'] ?>">
                            <div class="form-group">
                                <label>Product</label>
                                <select name="product_id" class="form-control">
                                    <?php while($p = mysqli_fetch_assoc($products)): ?>
                                        <option value="<?= $p['id'] ?>" <?= $p['id'] == $bom['product_id'] ? 'selected' : '' ?>>
                                            <?= $p['name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Version</label>
                                <input type="text" name="version" class="form-control" value="<?= $bom['version'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control"><?= $bom['description'] ?></textarea>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Materials
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="addRow()">+ Add Material</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table" id="materials-table">
                                        <thead><tr><th>Material</th><th>Qty</th><th>UOM</th><th>Action</th></tr></thead>
                                        <tbody>
                                            <?php foreach($bom_items as $item) { ?>
                                            <tr>
                                                <td><input type="text" name="material[]" class="form-control" value="<?= $item['material'] ?>" required></td>
                                                <td><input type="number" name="material_qty[]" class="form-control" step="0.01" value="<?= $item['quantity'] ?>" required></td>
                                                <td><input type="text" name="material_uom[]" class="form-control" value="<?= $item['uom'] ?>" required></td>
                                                <td><button type="button" onclick="this.closest('tr').remove()" class="btn btn-danger btn-sm">Remove</button></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <script><?= file_get_contents('create_row.js'); ?></script> <!-- optional externalize -->
                                </div>
                            </div>

                        </div>
                        
                        <div class="card-footer">
                            <div class="form-group float-end">
                                <a href="./" class="btn btn-default">Cancel</a>
                                <button type="submit" name="action" value="update" class="btn btn-success">Update BOM</button>
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
