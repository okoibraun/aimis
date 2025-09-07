<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/helpers.php';
require_once '../../functions/user_functions.php';

if (!isset($_SESSION['user_id']) || !is_superadmin($_SESSION['role'])) {
    redirect('../auth/login.php');
}

$users = get_all_users(); // Should exclude superadmin
$companies = get_all_companies(); // Optional filter

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $company_id = intval($_POST['company_id']);
    $granted_by = $_SESSION['user_id'];

    if (grant_cross_company_access($user_id, $company_id, $granted_by)) {
        $success = "Access granted successfully.";
    } else {
        $error = "Failed to grant access.";
    }
}
?>

<?php include '../../templates/header.php'; ?>
<?php include '../../templates/navbar.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Grant Cross-Company Access</h1>
  </section>

  <section class="content">
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <form method="POST">
      <div class="form-group">
        <label>User</label>
        <select name="user_id" class="form-control">
          <?php foreach ($users as $u): ?>
            <option value="<?= $u['id'] ?>"><?= $u['first_name'] ?> <?= $u['last_name'] ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group">
        <label>Company to Grant Access</label>
        <select name="company_id" class="form-control">
          <?php foreach ($companies as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Grant Access</button>
    </form>
  </section>
</div>

<?php include '../../templates/footer.php'; ?>
