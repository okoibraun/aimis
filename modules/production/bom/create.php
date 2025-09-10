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

$products = $conn->query("SELECT * FROM sales_products WHERE company_id = $company_id");

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
                    <h1>Create BOM</h1>
                </section>

                <section class="content">
                    <form action="save.php" method="post" class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Product</label>
    
                                <select name="product_id" class="form-control" required>
                                    <option value="">Select Product</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Version</label>
                                <input type="text" name="version" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="summernote" class="form-control"></textarea>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">Materials</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="addRow()">+ Add Material</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- BOM Materials -->
                                    <table class="table table-bordered" id="materials-table">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Qty</th>
                                                <th>UOM</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Injected by JS -->
                                        </tbody>
                                    </table>

                                    <script>
                                        function addRow() {
                                            const row = `<tr>
                                                <td><input type="text" name="material[]" class="form-control" required></td>
                                                <td><input type="number" name="material_qty[]" class="form-control" step="0.01" required></td>
                                                <td><input type="text" name="material_uom[]" class="form-control" required></td>
                                                <td><button type="button" onclick="this.closest('tr').remove()" class="btn btn-danger btn-sm">Remove</button></td>
                                            </tr>`;
                                            document.querySelector('#materials-table tbody').insertAdjacentHTML('beforeend', row);
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="form-group float-end">
                                <a href="./" class="btn btn-default">Cancel</a>
                                <button type="submit" name="action" value="create" class="btn btn-success">Save BOM</button>
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
