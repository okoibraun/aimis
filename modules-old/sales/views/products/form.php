<?php
require_once '../../includes/helpers.php';

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
          
            <section class="content-header">
                <h1><?= $is_edit ? 'Edit' : 'Add' ?> Product</h1>
            </section>

            <section class="content">
                <form action="../../../modules/sales/controllers/products.php" method="POST">
                    <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'add' ?>">
                    <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <?php endif; ?>
                    <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>
                        <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>
                        <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                        </div>
                        <div class="form-group">
                        <label>Discount Type</label>
                        <select name="discount_type" class="form-control">
                            <option value="none" <?= $product['discount_type'] == 'none' ? 'selected' : '' ?>>None</option>
                            <option value="percentage" <?= $product['discount_type'] == 'percentage' ? 'selected' : '' ?>>Percentage</option>
                            <option value="fixed" <?= $product['discount_type'] == 'fixed' ? 'selected' : '' ?>>Fixed</option>
                        </select>
                        </div>
                        <div class="form-group">
                        <label>Discount Value</label>
                        <input type="number" step="0.01" name="discount_value" class="form-control" value="<?= $product['discount_value'] ?>">
                        </div>
                        <div class="form-group">
                        <label>Bundle Group ID (optional)</label>
                        <input type="number" name="bundle_group_id" class="form-control" value="<?= $product['bundle_group_id'] ?>">
                        </div>
                        <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" <?= $product['is_active'] ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= !$product['is_active'] ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success">Save</button>
                        <a href="list.php" class="btn btn-secondary">Cancel</a>
                    </div>
                    </div>
                </form>
            </section>

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
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
