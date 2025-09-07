<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/helpers.php';

if (!isset($_SESSION['user_id'])) {
    redirect('../auth/login.php');
}

$access_list = get_cross_company_access($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['revoke_id'])) {
    $access_id = intval($_POST['revoke_id']);
    if (revoke_cross_company_access($access_id)) {
        $message = "Access revoked.";
        $access_list = get_cross_company_access($_SESSION['user_id']); // refresh
    }
}
?>

<?php include '../../templates/header.php'; ?>
<?php include '../../templates/navbar.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>My Cross-Company Access</h1>
  </section>

  <section class="content">
    <?php if (isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Company</th>
          <th>Granted On</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($access_list as $a): ?>
          <tr>
            <td><?= $a['target_company'] ?></td>
            <td><?= $a['granted_at'] ?></td>
            <td>
              <form method="POST" onsubmit="return confirm('Revoke access?')">
                <input type="hidden" name="revoke_id" value="<?= $a['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">Revoke</button>
              </form>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </section>
</div>

<?php include '../../templates/footer.php'; ?>
