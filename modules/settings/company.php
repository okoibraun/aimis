<?php
require_once '../../config/db.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/company_functions.php';
require_once '../../includes/audit_log.php';

//ensure_logged_in();
$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

// if (!has_permission('manage_company_settings')) {
//     die('Unauthorized.');
// }

$company = get_company_by_id($company_id);

if (isset($_POST['updateCompanyInfoBtn']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $industry = trim($_POST['industry']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    update_company_profile($company_id, $name, $industry, $address, $email, $phone);
    $company = get_company_by_id($company_id); // Refresh
    $_SESSION['success'] = "Company settings updated.";
}

if (isset($_POST['addCompanyLogoBtn']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle file upload for avatar
  if (!empty($_FILES['logo']['name'])) {
      $target_dir = "../../uploads/company/";
      $file_name = basename($_FILES['logo']['name']);
      $target_file = $target_dir . time() . '_' . $file_name;

      if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
          $company_logo_name = basename($target_file);
          $conn->query("UPDATE companies SET logo='$company_logo_name' WHERE id=$company_id");

          $_SESSION['company_logo'] = $company_logo_name;
          $_SESSION['success'] = "Company Logo Uploaded Successfully!";

          // Log Audit
          log_audit($conn, $user_id, 'Company Logo Upload', 'Uploaded their Company Logo.');
      } else {
          $errors[] = "Failed to upload Company Logo.";
      }
  }
}

if (isset($_POST['addAPIKeyBtn']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $api = $_POST;
  $api_key = $api['ai_api_key'];

  $update_api_key = $conn->prepare("UPDATE companies SET ai_api_key = ? WHERE id = ?");
  $update_api_key->bind_param("si", $api_key, $company_id);
  if($update_api_key->execute()) {
    $_SESSION['success'] = "AI API Key Updated Successfully";
  }
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
              <section class="content-header mt-3 mb-3">
                <h1>Company Settings</h1>
              </section>
              <section class="content">
                <div class="row">
                  <div class="col">
                    <div class="card card-info">
                      <div class="card-header"><h3 class="card-title">Edit Company Info</h3></div>
                      <form method="POST">
                        <div class="card-body">
                          <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                              <?= $_SESSION['success'] ?>
                              <?php unset($_SESSION['success']) ?>
                            </div>
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
                          <button type="submit" class="btn btn-info" name="updateCompanyInfoBtn">Update Company</button>
                        </div>
                      </form>
                    </div>
                  </div>

                  <div class="col-auto">
                    <div class="card">
                      <div class="card-header">
                        <h3 class="card-title">Upload Company Logo</h3>
                      </div>
                      <div class="card-body">
                        <?php if (isset($_SESSION['company_logo'])): ?>
                            <img src="/uploads/company/<?php echo htmlspecialchars($_SESSION['company_logo']); ?>" width="120" class="rounded-circle mb-3">
                        <?php else: ?>
                            <img src="/assets/images/users/default_user_avatar.jpg" width="120" class="rounded-circle mb-3">
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label>Company Logo (640 x 640 px)</label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary" name="addCompanyLogoBtn">Upload</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mt-4 mb-2">
                  <div class="col">
                    <form action="" method="post" class="card">
                      <div class="card-header">
                        <h3 class="card-title">
                          AI API Settings
                        </h3>
                      </div>
                      <div class="card-body">
                        <?php $api_key = $conn->query("SELECT ai_api_key FROM companies WHERE id = $company_id")->fetch_assoc(); ?>
                        <div class="form-group">
                          <label for="ai_api_key" class="mb-2">API Key:</label>
                          <input type="text" name="ai_api_key" class="form-control" value="<?= $api_key['ai_api_key'] ?>">
                        </div>
                      </div>
                      <div class="card-footer">
                        <div class="form-group float-end">
                          <button type="submit" name="addAPIKeyBtn" class="btn btn-primary">Save</button>
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
