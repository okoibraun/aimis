<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$product = ['name'=>'','description'=>'','price'=>'','discount_type'=>'none','discount_value'=>'0','bundle_group_id'=>'','is_active'=>1];
$is_edit = isset($_GET['id']);

if ($is_edit) {
    $product = $conn->query("SELECT * FROM sales_products WHERE id = {$_GET['id']}")->fetch_assoc();
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Products</title>
    <?php include_once("../../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
                <section class="content-header mt-3 mb-3">
                    <h1>
                        Add Product
                    </h1>
                </section>
    
                <section class="content">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="card">
                                        <form action="../../controllers/products.php" method="POST" class="card-content">
                                            <input type="hidden" name="action" value="add">
                                            <div class="card-header">
                                                <h5 class="card-title">New Product Details</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input name="name" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" id="summernote" cols="30" rows="3"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Discount Value</label>
                                                            <input type="number" step="0.01" name="discount_value" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Discount Type</label>
                                                            <select name="discount_type" class="form-control">
                                                                <option value="none">None</option>
                                                                <option value="percentage">Percentage</option>
                                                                <option value="fixed">Fixed</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            <select name="is_active" class="form-control">
                                                                <option value="1" selected>Active</option>
                                                                <option value="0">Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="tax_type">Tax Type</label>
                                                            <select name="tax_config_id" id="taxType" class="form-control">
                                                                <option data-rate="0" selected>None</option>
                                                                <?php $taxes = $conn->query("SELECT * FROM tax_config WHERE company_id = $company_id"); ?>
                                                                <?php foreach($taxes as $tax) { ?>
                                                                    <option value="<?= $tax['id'] ?>" data-rate="<?= $tax['rate'] ?>"><?= $tax['tax_type'] ?> - <?= $tax['description'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="tax_rate">Tax Rate (%)</label>
                                                            <input type="number" name="tax_rate" id="taxRate" class="form-control" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="form-group float-end">
                                                    <a href="./" class="btn btn-danger">Cancel</a>
                                                    <button class="btn btn-success">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
    
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            
        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../../includes/scripts.phtml"); ?>
    <?php include("../../../../includes/scripts.phtml"); ?>
    <script>
        const taxType = document.querySelector("#taxType");

        document.querySelector('#taxRate').value = taxType.options[taxType.selectedIndex].dataset.rate;

        taxType.addEventListener('change', () => {
            document.querySelector('#taxRate').value = taxType.options[taxType.selectedIndex].dataset.rate;
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
