<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
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

$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currency_code = mysqli_real_escape_string($conn, $_POST['currency_code']);
    $currency_name = mysqli_real_escape_string($conn, $_POST['currency_name']);
    $currency_symbol = mysqli_real_escape_string($conn, $_POST['currency_symbol']);
    $is_base = intval($_POST['is_base']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Insert new currency into the database
    $stmt = $conn->query("
        INSERT INTO currencies (company_id, user_id, employee_id, code, name, symbol, is_base_currency, description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iiisssis', $company_id, $user_id, $employee_id, $currency_code, $currency_name, $currency_symbol, $is_base, $description);
    $done = $stmt->execute();
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Currency</title>
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

          <section class="content-header">
            <h1>Add Currency</h1>
          </section>

          <section class="content">
          <?php if ($done): ?>
            <div class="alert alert-success">
                <a href="./" class="btn btn-secondary btn-sm float-end">Back</a>
                Currency saved successfully.
            </div>
          <?php endif; ?>

          <!-- Set Exchange Rate Form -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Enter Currency Details</h3>

            </div>
            <div class="card-body">
              <form method="POST">
                <div class="form-group">
                  <label>Currency Code</label>
                  <input type="text" name="currency_code" class="form-control" required>
                </div>

                <div class="form-group">
                  <label>Currency Name</label>
                  <input type="text" name="currency_name" class="form-control" required>
                </div>

                <div class="form-group">
                  <label>Currency Symbol</label>
                  <input type="text" name="currency_symbol" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="is_base">Is Base Currency</label>
                    <select name="is_base" id="" class="form-control">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group">
                  <label>Description</label>
                  <textarea name="description" id="" rows="10" class="form-control"></textarea>
                </div>

                <div class="form-group float-end mt-2">
                  <a href="./" class="btn btn-danger">Cancel</a>
                  <button type="submit" class="btn btn-primary">Save</button>
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
