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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $department = mysqli_real_escape_string($conn, $_POST['department']);

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['message'] = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, company_name, department) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $password, $role, $company_name, $department);
        $insert = $stmt->execute();

        if ($insert) {
            $_SESSION['success'] = "User [ {$name} ] added successfully!";
            // Log Audit
            include_once('../includes/audit_log.php');
            include_once('../functions/log_functions.php');
            log_audit($conn, $_SESSION['user_id'], 'Add User', 'Added user: '.$name);
            log_activity($_SESSION['user_id'], $_SESSION['company_id'], 'add_user', 'Added user: '.$name);
            header('Location: index.php');
        } else {
            $_SESSION['error'] = "Error adding user [ {$name} ].";
        }
    }
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

                            <h3 class="card-title align-item-center">Register New User</h3>
                            <div class="card-tools">
                                <a href="index.php" class="btn btn-secondary btn-round"> X </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" class="form-inline">
                                <div class="form-group mb-3">
                                    <label>Full Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Email Address</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required minlength="6">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="staff">Staff</option>
                                        <option value="guest">Guest</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <input type="hidden" name="role" value="staff">
                                <input type="hidden" name="company_name" value="Unknown Company">
                                <div class="form-group mb-3">
                                    <label>Department</label>
                                    <input type="text" name="department" class="form-control" required placeholder="e.g. HR, IT, Finance">
                                </div>
                                <div class="form-group float-end">
                                    <button type="submit" class="btn btn-success w-100">Register</button>
                                </div>
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