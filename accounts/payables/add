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

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor = mysqli_real_escape_string($conn, $_POST['vendor_id']);
    $bill_date = $_POST['bill_date'];
    $due_date = $_POST['due_date'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $total_amount = floatval($_POST['total_amount']);
    $reference = mysqli_real_escape_string($conn, $_POST['reference']);

    $stmt = mysqli_prepare($conn, "
        INSERT INTO bills (company_id, user_id, employee_id, vendor_id, bill_date, due_date, description, amount, reference)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'iiiisssis', $company_id, $user_id, $employee_id, $vendor, $bill_date, $due_date, $description, $total_amount, $reference);
    $success = mysqli_stmt_execute($stmt);
}

if(in_array($_SESSION['user_role'], system_users())) {
  $vendors = $conn->query("SELECT id, name FROM accounts_vendors");
} else {
  $vendors = $conn->query("SELECT id, name FROM accounts_vendors WHERE company_id = $company_id");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Add New Bill</title>
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
            <h1>Add New Vendor Bill</h1>
          </section>

          <section class="content">
            <?php if ($success): ?>
              <div class="alert alert-success">Bill successfully recorded.</div>
            <?php endif; ?>

            <div class="card">
              <div class="card-body">
                <form method="POST">
                  <div class="form-group">
                    <label>Vendor Name</label>
                    <select name="vendor_id" id="" class="form-control select2">
                      <option>-- Select Vendor --</option>
                      <?php foreach($vendors as $vendor) { ?>
                        <option value="<?= $vendor['id']; ?>"><?= $vendor['name']; ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Bill Date</label>
                    <input type="date" name="bill_date" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                  </div>

                  <div class="form-group">
                    <label>Total Amount</label>
                    <input type="number" step="0.01" name="total_amount" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label>Reference (Optional)</label>
                    <input type="text" name="reference" class="form-control">
                  </div>

                  <div class="form-group">
                    <a href="./" class="btn btn-danger">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Bill</button>
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
