<?php
require_once '../../config/db.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/company_functions.php';

//ensure_logged_in();
$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

// if (!has_permission('manage_company_settings')) {
//     die('Unauthorized.');
// }

$company = get_company_by_id($company_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $industry = trim($_POST['industry']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    update_company_profile($company_id, $name, $industry, $address, $email, $phone);
    $company = get_company_by_id($company_id); // Refresh
    $success = "Company settings updated.";
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Settings</title>
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
              <section class="content-header">
                <h1>Company Settings</h1>
              </section>
              <section class="content">
                <div class="card card-info">
                  <div class="card-header"><h3 class="card-title">Edit Company Info</h3></div>
                  <form method="POST">
                    <div class="card-body">
                      <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                      <?php endif; ?>
                      <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($company['name']) ?>" required>
                      </div>
                      <div class="form-group">
                        <label>Industry</label>
                        <input type="text" name="industry" class="form-control" value="<?= htmlspecialchars($company['industry']) ?>">
                      </div>
                      <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" value="<?= $company['address'] ?>">
                      </div>
                      <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $company['email'] ?>">
                      </div>
                      <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= $company['phone'] ?>">
                      </div>
                    </div>
                    <div class="card-footer">
                      <button type="submit" class="btn btn-info">Update Company</button>
                    </div>
                  </form>
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
