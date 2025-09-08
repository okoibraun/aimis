<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/auth_functions.php';

// Restrict access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    redirect('../auth/login.php');
}

// Get companies to show
if ($_SESSION['role'] === 'superadmin') {
    $companies = get_all_companies();
} else {
    $companies = get_companies_by_group($_SESSION['company_id']);
}
?>

<?php include '../../templates/header.php'; ?>
<?php include '../../templates/navbar.php'; ?>
<?php include '../../templates/sidebar.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1>Company List</h1>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <a href="create.php" class="btn btn-primary mb-3">Add New Company</a>

      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Company Name</th>
            <th>Parent Company</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($companies as $company): ?>
          <tr>
            <td><?= $company['id']; ?></td>
            <td><?= htmlspecialchars($company['name']); ?></td>
            <td><?= $company['parent_name'] ?? '—'; ?></td>
            <td><?= $company['created_at']; ?></td>
            <td>
              <a href="edit.php?id=<?= $company['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="delete.php?id=<?= $company['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<?php include '../../templates/footer.php'; ?>
