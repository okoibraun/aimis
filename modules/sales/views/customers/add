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

            <section class="row content">
                <div class="col-lg-8">
                    <div class="card">
                        <form action="../../controllers/customers.php" method="POST" class="card-content">
                            <input type="hidden" name="action" value="add">
                            <div class="card-header">
                                <h5 class="card-title">New Customer Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input name="phone" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input name="address" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input name="city" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Country</label>
                                    <input name="country" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Tax ID</label>
                                    <input name="tax_id" class="form-control">
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
                                <a href="./" class="btn btn-danger float-end">Cancel</a>
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
