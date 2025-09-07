<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$products = get_all_rows('sales_products');
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


          <section class="content mt-5">
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col-lg-6">
                    <h4>Products</h4>
                  </div>
                  <div class="col-lg-6 text-end">
                    <!-- <button class="btn btn-primary" data-toggle="modal" data-target="#productModal">Add Product</button> -->
                    <a href="add_product.php" class="btn btn-primary">Add Product</a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <table class="table table-hover table-striped">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Price</th>
                      <th>Discount</th>
                      <th>Bundle Group</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($products as $row) { ?>
                      <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['discount_type'] . ' (' . $row['discount_value'] . ')' ?></td>
                        <td><?= $row['bundle_group_id'] ?: '-' ?></td>
                        <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
                        <td>
                          <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                          <form action="../../controllers/products.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
                          </form>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
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
