<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/auth_functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    redirect('../../login.php');
}

$company_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$company = get_company_by_id($company_id);

if (!$company || !user_can_manage_company($_SESSION, $company)) {
    die("Unauthorized or company not found.");
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = sanitize_input($_POST['company_name']);
    $industry = sanitize_input($_POST['industry']);
    $is_parent = sanitize_input($_POST['is_parent']);
    $description = sanitize_input($_POST['description']);

    if (empty($new_name)) {
        $errors[] = "Company name is required.";
    }

    if (empty($errors)) {
        //if (update_company_name($company_id, $new_name)) {
        if(mysqli_query($conn, "UPDATE companies SET name='$new_name', industry='$industry', is_parent='$is_parent', description='$description' WHERE id=$company_id")) {
          $company['name'] = $new_name;
          $company['industry'] = $industry;
          $company['is_parent'] = $is_parent;
          $company['description'] = $description;
          $success = true;
        } else {
            $errors[] = "Update failed.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Edit Company</title>
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
              <section class="content-header mb-3">
                <div class="container-fluid">
                  <h1>Edit Company</h1>
                </div>
              </section>

              <section class="content">
                <div class="container-fluid">

                  <?php if ($success): ?>
                    <div class="alert alert-success">Company updated successfully.</div>
                  <?php endif; ?>

                  <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
                  <?php endif; ?>

                  <form method="post">
                    <div class="card">
                      <!-- Card Header -->
                      <div class="card-header">
                        <div class="card-toolbar">
                          <a href="index.php" class="btn btn-sm btn-secondary float-end">Back</a>
                        </div>
                      </div>
                      <!-- Card Body -->
                      <div class="card-body">
                        <div class="form-group mb-2">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" name="company_name" id="company_name" value="<?= htmlspecialchars($company['name']); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="form-group mb-2">
                                    <label for="industry">Industry</label>
                                    <input type="text" name="industry" class="form-control" value="<?= htmlspecialchars($company['industry']); ?>" placeholder="Industry">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group mb-2">
                                    <label for="industry">Is Parent</label>
                                    <select name="is_parent" class="form-control select2">
                                        <option value="0" <?= ($company['is_parent'] == 0) ? "selected" : ""; ?>>No</option>
                                        <option value="1" <?= ($company['is_parent'] == 1) ? "selected" : ""; ?>>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                              <label for="description">Description</label>
                              <textarea name="description" class="form-control" row="5"><?= $company['description']; ?></textarea>
                        </div>
                      </div>
                      <!-- Card footer -->
                      <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-end">Update Company</button>
                      </div>
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
