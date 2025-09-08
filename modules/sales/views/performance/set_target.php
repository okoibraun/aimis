<?php
require_once '../../includes/helpers.php'; // Include your helper functions
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $month = date('Y-m', strtotime($month)); // Ensure the month is in Y-m format
    $amount = $_POST['target_amount'];
    
    $db->query("INSERT INTO sales_targets (company_id, user_id, target_month, target_amount) VALUES ($company_id, $user_id, NOW(), $amount)");
    
    redirect('./');
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Targets</title>
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
          

          <div class="content-wrapper">
            <section class="content-header">
                <h1>Set Sales Target</h1>
            </section>
            <section class="content">
                <form method="post" class="box box-primary p-3">
                    <div class="form-group">
                    <label for="user_id">Sales Person</label>
                    <select name="user_id" class="form-control select2">
                        <option>-- Select Staff --</option>
                        <?php foreach ($db->query("SELECT id, name FROM users WHERE company_id = $company_id AND role = 'sales'") as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                    <div class="form-group">
                      <label for="target_month">Target Month</label>
                      <input type="date" name="target_month" class="form-control" required />
                    </div>
                    <div class="form-group">
                      <label for="target_amount">Target Amount</label>
                      <input type="number" name="target_amount" class="form-control" required />
                    </div>
                    <button class="btn btn-primary">Save</button>
                    <a href="./" class="btn btn-default">Cancel</a>
                </form>
            </section>

          </div>
          

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
