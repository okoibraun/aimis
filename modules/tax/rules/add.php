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
$page = "add";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Rules</title>
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
                <h3>Tax - Rules</h3>
              </section>

              <section class="content mt-3">
                <form action="save.php" method="POST" class="card">
                  <input type="hidden" name="id" id="rule_id">
                  <div class="card-header">
                    <h5 class="card-title">Country-Specific Rule</h5>
                    <div class="card-tools">
                        <a href="./" class="btn btn-danger btn-sm">&times;</a>
                    </div>
                  </div>

                  <div class="card-body">
                    <div class="form-group">
                      <label>Country</label>
                      <input type="text" name="country" id="rule_country" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>Template Name</label>
                      <input type="text" name="template_name" id="rule_template" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>BEPS Compliant?</label>
                      <select name="beps_compliant" id="rule_beps" class="form-control">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Status</label>
                      <select name="is_active" id="rule_active" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                      </select>
                    </div>
                  </div>

                  <div class="card-footer">
                    <div class="form-group float-end">
                        <a href="./" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-success">Save Rule</button>
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
