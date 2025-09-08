<?php
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/auth_functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $email = sanitize_input($_POST['email']);
    $email = sanitize_input(mysqli_real_escape_string($mysqli, $_POST['email']));
    // $password = sanitize_input($_POST['password']);
    $password = sanitize_input(mysqli_real_escape_string($mysqli, $_POST['password']));

    $user = login_user($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['company_id'] = $user['company_id'];
        $_SESSION['role'] = $user['role'];

        // Log login
        require_once '../../functions/log_functions.php';
        log_activity($user['id'], $user['company_id'], 'login', 'User logged in');

        redirect('../dashboard/index.php');
    } else {
        $errors[] = "Invalid credentials.";
    }
}
?>

<?php include '../../templates/header.php'; ?>
<div class="login-box">
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to AIMIS</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?= implode('<br>', $errors) ?>
        </div>
      <?php endif; ?>

      <form method="post">
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>
        <div class="row">
          <div class="col-8"></div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Login</button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
<?php include '../../templates/footer.php'; ?>
