<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');
include("../functions/helpers.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$id = intval($_GET['id']);
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));

if (!$user) {
    header('Location: index.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update = mysqli_query($conn, "UPDATE users SET name='$name', email='$email', role='$role', department='$department', status='$status' WHERE id=$id");

    if ($update) {
        $message = "User updated successfully!";
    } else {
        $message = "Failed to update user.";
    }

    //Log Audit
    include_once('../includes/audit_log.php');
    include_once('../functions/log_functions.php');
    log_audit($conn, $_SESSION['user_id'], 'Edit User', 'Edit User for user ID: '.$id);
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Users</title>
    <?php include_once("../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../includes/sidebar.phtml"); ?>
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
            <div class="row mt-4">
                <!-- Start col 8 -->
                <div class="col-lg-8">

                    <div class="card shadow">
                        <div class="card-header">

                            <h3 class="card-title align-item-center">Edit User</h3>
                            <div class="card-tools">
                                <a href="index.php" class="btn btn-secondary btn-round"> X </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group mb-3">
                                    <label>Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                        <option value="staff" <?php if ($user['role'] == 'staff') echo 'selected'; ?>>Staff</option>
                                        <option value="student" <?php if ($user['role'] == 'student') echo 'selected'; ?>>Student</option>
                                        <option value="guest" <?php if ($user['role'] == 'guest') echo 'selected'; ?>>Guest</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Department</label>
                                    <input type="text" name="department" value="<?php echo htmlspecialchars($user['department']); ?>" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" <?php if ($user['status'] == 'active') echo 'selected'; ?>>Active</option>
                                        <option value="inactive" <?php if ($user['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>

                </div>
                <!-- /.Start col -->
                <!-- Last Col 4 -->
                <div class="col-lg-4">

                </div>
                <!-- /. Last col 4 -->
            </div>
            <!-- /.row (main row) -->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>