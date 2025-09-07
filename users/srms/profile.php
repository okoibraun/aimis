<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');
include("../functions/helpers.php");
include_once('../includes/audit_log.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch users
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = mysqli_query($conn, "UPDATE users SET name='$name', email='$email' WHERE id=$user_id");

    if ($update) {
        $message = "Profile updated!";
        $_SESSION['user_name'] = $name; // update session too
        //Log Audit
        log_audit($conn, $user_id, 'Profile Update', 'Updated their own profile.');
    } else {
        $message = "Failed to update.";
    }

    // Handle file upload for avatar
    if (!empty($_FILES['avatar']['name'])) {
        $target_dir = "../uploads/users/";
        $file_name = basename($_FILES['avatar']['name']);
        $target_file = $target_dir . time() . '_' . $file_name;
    
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
            $profile_pic_name = basename($target_file);
            mysqli_query($conn, "UPDATE users SET avatar='$profile_pic_name' WHERE id=$user_id");
    
            //Log Audit
            log_audit($conn, $user_id, 'Profile Picture Update', 'Updated their profile picture.');
        }
    }
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Users - Profile</title>
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
                        <div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-info alert-dismissible fade show mt-5" role="alert">
                            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show mt-5" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- end row -->

            <div class="container mt-5">
    <h2>My Account</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Display Profile Picture -->
    <?php if ($user['avatar']): ?>
        <img src="../uploads/users/<?php echo htmlspecialchars($user['avatar']); ?>" width="120" class="rounded-circle mb-3">
    <?php else: ?>
        <img src="../assets/images/users/default_profile.png" width="120" class="rounded-circle mb-3">
    <?php endif; ?>
    <!-- / End Display Profile Picture -->

    <form method="post">
        <div class="form-group mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="form-group mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group mb-3">
            <label>Profile Picture</label>
            <input type="file" name="avatar" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>

    <h3>Change Password</h3>
    <form method="post" action="change_password.php">
        <div class="form-group mb-3">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 characters" required>
        </div>
        <button type="submit" class="btn btn-warning">Update Password</button>
    </form>

</div>

            <!--begin::Row-->
            
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