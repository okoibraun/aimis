<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$super_roles = super_roles();

if (!in_array($_SESSION['role'], $super_roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $create_folder = mysqli_query($conn, "INSERT INTO folders (company_id, name, created_by) VALUES ($company_id, '$name', $user_id)");
    if ($create_folder) {
        // Get the last inserted ID
        $id = mysqli_insert_id($conn);

        // Log Audit
        include_once('../../includes/audit_log.php');
        include_once('../../functions/log_functions.php');
        log_activity($user_id, $company_id, 'create_folder', "Created Folder: {$name}");

        $_SESSION['success'] = "Folder created successfully.";
        // Redirect to the folders index page
        header('Location: ./');
        exit();
    } else {
        $_SESSION['error'] = "Error creating folder: " . mysqli_error($conn);
    }
    header('Location: ./');
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memos - Folders</title>
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
                    <h2 class="mt-4">Create New Folder</h2>
                    <form method="POST" class="col-lg-6 mt-4">
                        <div class="form-group">
                            <label>Folder Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <button class="btn btn-success">Create Folder</button>
                        <a href="./" class="btn btn-danger">Cancel</a>
                    </form>
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
