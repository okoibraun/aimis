<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

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

// Fetch all tax configs for current company
$id = isset($_GET['id']) ? $_GET['id'] : null;
$tax = $conn->query("SELECT * FROM tax_config WHERE id = $id AND company_id = $company_id")->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Setup</title>
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
                <h3>Tax - Setup (VAT / GST / WHT)</h3>
              </section>

              <section class="content">
                <?php include("../../../includes/alert.phtml"); ?>

                <form action="save.php" method="POST" class="card">
                  <input type="hidden" name="id" id="tax_id" value="<?= $tax['id'] ?>">
                  <div class="card-header">
                    <h5 class="card-title">Tax Rule</h5>
                    <div class="card-tools">
                        <a href="./" class="btn btn-danger btn-sm">&times;</a>
                    </div>
                  </div>

                  <div class="card-body">
                    <div class="form-group">
                      <label>Tax Type</label>
                      <select name="tax_type" id="tax_type" class="form-control" required>
                        <option value="VAT" <?= $tax['tax_type'] == "VAT" ? 'selected' : '' ?>>VAT</option>
                        <option value="GST" <?= $tax['tax_type'] == "GST" ? 'selected' : '' ?>>GST</option>
                        <option value="WHT" <?= $tax['tax_type'] == "WHT" ? 'selected' : '' ?>>Withholding Tax</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Rate (%)</label>
                      <input type="number" name="rate" id="tax_rate" step="0.01" class="form-control" value="<?= $tax['rate'] ?>" required>
                    </div>
                    <div class="form-group">
                      <label>Description</label>
                      <input type="text" name="description" id="tax_desc" class="form-control" value="<?= $tax['description'] ?>">
                    </div>
                    <div class="form-group">
                      <label>Status</label>
                      <select name="is_active" id="tax_active" class="form-control">
                        <option value="1" <?= $tax['is_active'] == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $tax['is_active'] == 0 ? 'selected' : '' ?>>Inactive</option>
                      </select>
                    </div>
                  </div>

                  <div class="card-footer">
                    <div class="form-group float-end">
                        <a href="./" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-success">Save Tax</button>
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
