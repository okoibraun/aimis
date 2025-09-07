<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
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
          
            <section class="content-header">
                <h1><?= $is_edit ? 'Edit' : 'Add' ?> Product</h1>
            </section>

            <section class="row content">
                <div class="col-lg-8">
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
                                    <label>Price</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Discount Type</label>
                                    <select name="discount_type" class="form-control">
                                        <option value="none">None</option>
                                        <option value="percentage">Percentage</option>
                                        <option value="fixed">Fixed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Discount Value</label>
                                    <input type="number" step="0.01" name="discount_value" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="is_active" class="form-control">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-success float-end">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            
            
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
