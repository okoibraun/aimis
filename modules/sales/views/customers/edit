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

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$customer = ['company_name'=>'','email'=>'','phone'=>'','address'=>'','city'=>'','country'=>'','tax_id'=>'','is_active'=>1];
$is_edit = isset($_GET['id']);

if ($is_edit) {
    $customer = get_row_by_id('sales_customers', $_GET['id']);
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Customers</title>
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
                <h1><?= $is_edit ? 'Edit' : 'Add' ?> Customer</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-8">
                        <form action="../../controllers/customers.php" method="POST">
                    <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'add' ?>">
                    <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                    <?php endif; ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Customer Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control"><?= htmlspecialchars($customer['address']) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($customer['city']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($customer['country']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Tax ID / VAT</label>
                                <input type="text" name="tax_id" class="form-control" value="<?= htmlspecialchars($customer['tax_id']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" <?= $customer['is_active'] ? 'selected' : '' ?>>Active</option>
                                    <option value="0" <?= !$customer['is_active'] ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success">Save</button>
                            <a href="./" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
                    </div>
                    <div class="col-4"></div>
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
