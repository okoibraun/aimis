<?php
require_once '../../config/db.php';
require_once '../../functions/auth_functions.php';
require_once '../../functions/user_functions.php';
require_once '../../includes/audit_log.php';
require_once '../../functions/log_functions.php';
include("../../functions/role_functions.php");

// ensure_logged_in();
$user_id = $_SESSION['user_id'];
$user = get_user_by_id($user_id);

$message = '';
$password_change = false;

if (isset($_POST['updtProfileBtn']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    //update_user_profile($user_id, $name, $email, $password);
    $update = $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$user_id");
    if ($update) {
        $user = get_user_by_id($user_id); // Refresh after update
        $success = "Profile updated successfully!";
        log_audit($conn, $user_id, 'Profile Update', 'Updated their own profile.');
    } else {
        $errors[] = "Failed to update profile.";
    }
}

if (isset($_POST['chngPasswordBtn']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);

    $errors = [];

    if ($password && $password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    
    if (empty($errors)) {
      $user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
  
      if(password_verify($current_password, $user['password'])) {
        if(preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);
          $conn->query("UPDATE users SET password='$hashed_password' WHERE id=$user_id");
          
          // Audit Log
          log_audit($conn, $user_id, 'Password Change', 'Changed own password.');
          
          $success = "Password changed successfully.";
          $password_change = true;
        } else {
          $errors[] = "Password does not meet strength requirements.";
        }
      } else {
        $errors[] = "Current password is incorrect.";
      }
    }
}

if (isset($_POST['chngAvatarBtn']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle file upload for avatar
  if (!empty($_FILES['avatar']['name'])) {
      $target_dir = "../../uploads/users/";
      $file_name = basename($_FILES['avatar']['name']);
      $target_file = $target_dir . time() . '_' . $file_name;

      if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
          $profile_pic_name = basename($target_file);
          $conn->query("UPDATE users SET avatar='$profile_pic_name' WHERE id=$user_id");

          $_SESSION['user_avatar'] = $profile_pic_name;
          $user['avatar'] = $_SESSION['user_avatar'];
          $success = "Profile Picture Updated Successfully!";

          // Log Audit
          log_audit($conn, $user_id, 'Profile Picture Update', 'Updated their profile picture.');
      } else {
          $errors[] = "Failed to upload profile picture.";
      }
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

        <div class="container-fluid mt-5">
          <div class="content-wrapper">
            <section class="content-header">
              <h1>My Profile</h1>
            </section>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger"><?php echo implode('<br>', $errors); $errors[] = ''; ?></div>
            <?php elseif (!empty($success)): ?>
              <div class="alert alert-success"><?= $success ?> <?php $success = ""; ?></div>
            <?php endif; ?>

            <section class="content">
              <div class="row">
                <div class="col-md-4">
                  <div class="card card-primary">
                    <div class="card-header">
                      <h3 class="card-title">Update Profile</h3>
                    </div>
                    <div class="card-body">
                      
                      <form method="post">
                        <div class="form-group mb-3">
                          <label>Full Name</label>
                          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                          <label>Email</label>
                          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary" name="updtProfileBtn">Save Changes</button>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Upload Profile Picture</h3>
                    </div>
                    <div class="card-body">
                      <?php if ($user['avatar']): ?>
                          <img src="/uploads/users/<?php echo htmlspecialchars($user['avatar']); ?>" width="120" class="rounded-circle mb-3">
                      <?php else: ?>
                          <img src="/assets/images/users/default_user_avatar.jpg" width="120" class="rounded-circle mb-3">
                      <?php endif; ?>
                      <form method="post" enctype="multipart/form-data">
                          <div class="form-group mb-3">
                              <label>Profile Picture</label>
                              <input type="file" name="avatar" class="form-control">
                          </div>
                          <button type="submit" class="btn btn-primary" name="chngAvatarBtn">Upload</button>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Change Password</h3>
                    </div>
                    <div class="card-body">
                      <form method="post">
                          <div class="form-group mb-3">
                              <label>Current Password</label>
                              <input type="password" name="current_password" class="form-control" required>
                          </div>
                          <div class="form-group mb-3">
                              <label>New Password</label>
                              <input type="password" name="new_password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 characters" required>
                          </div>
                          <div class="form-group mb-3">
                              <label>Confirm Password</label>
                              <input type="password" name="confirm_password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 characters" required>
                          </div>
                          <button type="submit" class="btn btn-warning" name="chngPasswordBtn">Change Password</button>
                      </form>
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
    <?php if($password_change) { ?>
    <script>
        let logout = confirm("Do you want to logout now?");
        if(logout) {
          window.location.href = "/logout.php";
        }
    </script>
    <?php } ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
