<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "edit";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$product = ['name'=>'','description'=>'','price'=>'','discount_type'=>'none','discount_value'=>'0','bundle_group_id'=>'','is_active'=>1];
$is_edit = isset($_GET['id']);

if ($is_edit) {
    $product = get_row_by_id('sales_products', $_GET['id']);
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
                        Edit Product
                    </h1>
                </section>
    
                <section class="content">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <form action="../../controllers/products.php" method="POST">
                                        <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'add' ?>">
                                        <?php if ($is_edit): ?>
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <?php endif; ?>
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Product Details</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Product Name</label>
                                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" id="summernote" class="form-control"><?= $product['description'] ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Discount Value</label>
                                                            <input type="number" step="0.01" name="discount_value" class="form-control" value="<?= $product['discount_value'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Discount Type</label>
                                                            <select name="discount_type" class="form-control">
                                                                <option value="none" <?= $product['discount_type'] == 'none' ? 'selected' : '' ?>>None</option>
                                                                <option value="percentage" <?= $product['discount_type'] == 'percentage' ? 'selected' : '' ?>>Percentage</option>
                                                                <option value="fixed" <?= $product['discount_type'] == 'fixed' ? 'selected' : '' ?>>Fixed</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            <select name="is_active" class="form-control">
                                                                <option value="1" <?= $product['is_active'] ? 'selected' : '' ?>>Active</option>
                                                                <option value="0" <?= !$product['is_active'] ? 'selected' : '' ?>>Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group mt-4">
                                                            <div class="form-check form-switch">
                                                                <label for="priceIncludesTax" class="form-check-label">Price Includes TAX</label>
                                                                <input type="checkbox" name="price_includes_tax" class="form-check-input" value="1" id="priceTax" <?= ($product['price_includes_tax'] == 1) ? 'checked' : ''; ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col" id="showTax">
                                                        <div class="form-group">
                                                            <label for="tax_rate">Tax Rate (%)</label>
                                                            <input type="number" name="tax_rate" id="" step="0.01" class="form-control" value="<?= $product['tax_rate']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="form-group float-end">
                                                    <button class="btn btn-success">Save</button>
                                                    <a href="./" class="btn btn-secondary">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>
            </div>
            
            <?php include("_modal.php"); ?>
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
    <script>
        const showTax = document.querySelector("#showTax");
        const priceTax = document.querySelector('#priceTax');

        //showTax.style.display = "none";
        if(priceTax.checked) {
            showTax.style.display = "none";
        }
        priceTax.addEventListener('change', () => {
            if(!priceTax.checked) {
                showTax.style.display = "block";
            } else {
                showTax.style.display = "none";
            }
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
