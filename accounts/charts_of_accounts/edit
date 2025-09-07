<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
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

$company_id = $_SESSION['company_id'];

$id = $_GET['id'];
$account = $conn->query("SELECT * FROM accounts WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['account_code'];
    $name = $_POST['account_name'];
    $type = $_POST['account_type'];
    $parent = $_POST['parent_account_id'] ?? NULL;

    $stmt = $conn->prepare("UPDATE accounts SET account_code=?, account_name=?, account_type=?, parent_account_id=? WHERE id=?");
    $stmt->bind_param("sssii", $code, $name, $type, $parent, $id);
    $stmt->execute();
    header("Location: ./");
    exit();
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts</title>
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
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <!-- Start col -->
                <div class="col-lg-12 connectedSortable">
                    <!-- Page Content -->
                    <section class="content-header mt-3 mb-5">
                      <h1>Edit Account</h1>
                    </section>
                    <section class="content col-lg-6">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">
                            Edit Account Details
                          </h3>
                        </div>
                        <div class="card-body">
                          <form method="POST" action="">
                            <div class="form-group">
                              <label>Account Code</label>
                              <input type="text" name="account_code" class="form-control" value="<?= $account['account_code'] ?>" required>
                            </div>
                            <div class="form-group">
                              <label>Account Name</label>
                              <input type="text" name="account_name" class="form-control" value="<?= $account['account_name'] ?>" required>
                            </div>
                            <div class="form-group">
                              <label>Account Type</label>
                              <select name="account_type" class="form-control" required>
                                <?php
                                $types = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
                                foreach ($types as $type) {
                                  $selected = ($account['account_type'] == $type) ? 'selected' : '';
                                ?>
                                  <option <?= $selected ?>><?= $type ?></option>;
                                <?php } ?>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Parent Account</label>
                              <select name="parent_account_id" class="form-control">
                                <option value="">None</option>
                                <?php
                                  $res = mysqli_query($conn, "SELECT id, account_name FROM accounts WHERE company_id = $company_id AND id != $id");
                                  foreach ($res as $r) {
                                    $selected = ($account['parent_account_id'] == $r['id']) ? 'selected' : '';
                                    echo "<option value='{$r['id']}' $selected>{$r['account_name']}</option>";
                                  }
                                ?>
                              </select>
                            </div>
                            <button type="submit" class="btn btn-success">Update</button>
                            <a href="./" class="btn btn-secondary">Cancel</a>
                          </form>
                        </div>
                      </div>
                    </section>
                    <!-- /.Page Content -->
                </div>
                <!-- /.Start col -->
            </div>
            <!-- /.row (main row) -->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
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
