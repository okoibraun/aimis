<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/auth_functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = sanitize_input($_POST['company_name']);
    $user_name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);

    // Basic validation
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $company_id = create_company($company_name);

        if ($company_id) {
            $user_id = create_user($user_name, $email, $password, $company_id, 'admin');
            if ($user_id) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['company_id'] = $company_id;
                $_SESSION['role'] = 'admin';
                redirect('../dashboard/index.php');
            } else {
                $errors[] = "Failed to create user.";
            }
        } else {
            $errors[] = "Failed to create company.";
        }
    }
}
?>

<?php include '../../templates/header.php'; ?>
<div class="register-box">
  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg">Register a New Company</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="input-group mb-3">
          <input type="text" name="company_name" class="form-control" placeholder="Company Name" required>
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-building"></i></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="text" name="name" class="form-control" placeholder="Your Name" required>
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-user"></i></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-envelope"></i></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-lock"></i></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
          <div class="input-group-append"><div class="input-group-text"><i class="fas fa-lock"></i></div></div>
        </div>

        <div class="row">
          <div class="col-8"></div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
        </div>
      </form>

      <a href="login.php" class="text-center d-block mt-3">Already registered? Login</a>
    </div>
  </div>
</div>
<?php include '../../templates/footer.php'; ?>
