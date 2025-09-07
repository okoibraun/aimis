<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/auth_functions.php';

// Ensure user is logged in
// if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
//     redirect('../auth/login.php');
// }

$errors = [];
$success = false;

// Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = sanitize_input($_POST['company_name']);
    $industry = sanitize_input($_POST['industry']);
    $is_parent = isset($_POST['is_parent']) ? (int)$_POST['is_parent'] : 0;
    $description = sanitize_input($_POST['description']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (empty($company_name)) {
        $errors[] = "Company name is required.";
    }

    if (empty($errors)) {
        $parent_company_id = $_SESSION['company_id'] ?? null;

        // If superadmin, no parent company binding
        // if ($_SESSION['role'] === 'superadmin') {
        if (in_array($_SESSION['role'], ['superadmin', 'system'])) {
            $parent_company_id = null;
        }

        $new_company_id = create_company($user_id, $company_name, $industry, $is_parent, $description, $parent_company_id);

        if ($new_company_id) {
            $update_user = $conn->query("UPDATE users SET company_id = $new_company_id, company_name = '{$company_name}' WHERE id = $user_id");
            if ($update_user) {
                $_SESSION['company_id'] = $new_company_id;
                $_SESSION['company_name'] = $company_name;
                
                $success = true;
            }
        } else {
            $errors[] = "Failed to create company.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memos Dashboard</title>
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
                <section class="content-header mt-3">
                    <div class="container-fluid">
                        <h1>Create New Company</h1>
                    </div>
                </section>

                <section class="content">
                    <div class="container-fluid">

                    <?php if ($success): ?>
                        <div class="alert alert-success">Company created successfully.</div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><?= implode('<br>', $errors); ?></div>
                    <?php endif; ?>

                        <div class="row">
                          <div class="col-lg-8">
                            <div class="card">
                              <div class="card-header">
                                  <h3 class="card-title">Enter Company Details</h3>
                              </div>
                                  
                              <div class="card-body">
                                  <form method="post" class="form-horizontal">
                                      <div class="form-group mb-2">
                                          <label for="company_name">Company Name</label>
                                          <input type="text" class="form-control" name="company_name" id="company_name" required>
                                      </div>
                                      <div class="row">
                                          <div class="col-lg-9">
                                              <div class="form-group mb-2">
                                                  <label for="industry">Industry</label>
                                                  <input type="text" name="industry" class="form-control" placeholder="Industry">
                                              </div>
                                          </div>
                                          <div class="col-lg-3">
                                              <div class="form-group mb-2">
                                                  <label for="industry">Is Parent</label>
                                                  <select name="is_parent" class="form-control">
                                                      <option value="0">No</option>
                                                      <option value="1">Yes</option>
                                                  </select>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="form-group mb-2">
                                            <label for="description">Description</label>
                                            <textarea name="description" class="form-control" row="5"></textarea>
                                      </div>
                                      <button type="submit" class="btn btn-primary float-end">Create Company</button>
                                  </form>
                              </div>
                            </div>
                          </div>
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
