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
$page = "list" ?? "index" ?? "manage";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$super_roles = super_roles();

if (!in_array($_SESSION['role'], $super_roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// Fetch Folders
if(in_array($_SESSION['user_role'], system_users())) {
    // Fetch Memo Folders by Company if user is either admin or superadmin
    $folders = mysqli_query($conn, "SELECT * FROM folders ORDER BY created_at DESC");
} else if(in_array($_SESSION['user_role'], super_roles())) {
    // Fetch Memo Folders by Company if user is either admin or superadmin
    $folders = mysqli_query($conn, "SELECT * FROM folders WHERE company_id = $company_id ORDER BY created_at DESC");
} else {
    // Fetch User folders
    $folders = mysqli_query($conn, "SELECT * FROM folders WHERE company_id = $company_id AND created_by = $user_id ORDER BY created_at DESC");
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

            <!-- begin row -->
            <div class="row">
                <div class="col-lg-12">
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- end row -->

            <!--begin::Row-->
            <div class="row mt-5">
                <!-- Start col -->
                <div class="col-lg-12">

                    <div class="card">
                      <div class="card-header">
                          <div class="row">
                            <div class="col-lg-6">
                                <h4>Memo Folders</h4>
                            </div>
                            <div class="col-lg-6 float-end">
                                <div class="card-tools float-end">
                                    <a href="create" class="btn btn-primary">Create New Folder</a>
                                </div>  
                            </div>
                          </div>
                      </div>
                      <div class="card-body">
                          <div class="table-responsive">
                              <table class="table table-bordered">
                                  <thead>
                                      <tr>
                                          <th>Folder Name</th>
                                          <th>Actions</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                  <?php while ($folder = mysqli_fetch_assoc($folders)): ?>
                                      <tr>
                                          <td><?php echo htmlspecialchars($folder['name']); ?></td>
                                          <td>
                                              <a href="delete?id=<?php echo $folder['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                          </td>
                                      </tr>
                                  <?php endwhile; ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                    </div>
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
