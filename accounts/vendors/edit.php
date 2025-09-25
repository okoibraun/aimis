<?php
require_once '../../modules/sales/includes/helpers.php';
include("../../functions/role_functions.php");

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


$is_edit = isset($_GET['id']);
if ($is_edit) {
    $id = $_GET['id'];
    $vendor = $conn->query("SELECT * FROM accounts_vendors WHERE id = $id AND company_id = $company_id LIMIT 1")->fetch_assoc();
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Vendors</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="content-wrapper">
                <section class="content-header mt-3 mb-3">
                    <h1>Edit Vendor</h1>
                </section>

                <section class="content">
                    <div class="col-6">
                        <div class="card">
                            <form action="vendorsController" method="POST" class="card-content">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= $vendor['id'] ?>">
                                <div class="card-header">
                                    <h5 class="card-title">endor Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Vendor Name</label>
                                        <input type="text" name="name" class="form-control" value="<?= $vendor['name']; ?>" required>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= $vendor['email']; ?>">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input type="text" name="phone" class="form-control" value="<?= $vendor['phone']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" name="address" class="form-control" value="<?= $vendor['address']; ?>">
                                    </div>
    
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>City</label>
                                                <input type="text" name="city" class="form-control" value="<?= $vendor['city']; ?>">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>State</label>
                                                <input type="text" name="state" class="form-control" value="<?= $vendor['state']; ?>">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <input type="text" name="country" class="form-control" value="<?= $vendor['country']; ?>">
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Tax ID</label>
                                                <input type="text" name="tax_id" class="form-control" value="<?= $vendor['tax_id']; ?>">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-check form-switch mt-4">
                                                <label class="form-check-label" for="taxExempt">Tax Exempt</label>
                                                <input type="checkbox" name="tax_exempt" id="taxExempt" class="form-check-input" value="1" <?= ($vendor['tax_exempt'] == 1) ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="is_active" class="form-control">
                                                    <option value="1" <?= $vendor['is_active'] ? 'selected' : '' ?>>Active</option>
                                                    <option value="0" <?= !$vendor['is_active'] ? 'selected' : '' ?>>Inactive</option>
                                                </select>
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
                </section>
            </div>

          
        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
