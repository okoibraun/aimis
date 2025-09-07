<?php
require_once '../../functions/auth_functions.php';
require_once '../../functions/company_functions.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$companies = get_user_companies($user_id);

include '../../templates/header.php';
include '../../templates/navbar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>My Companies</h1>
    </section>
    <section class="content">
        <a href="create.php" class="btn btn-success mb-3">+ Add New Company</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Industry</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($company = $companies->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($company['name']) ?></td>
                        <td><?= htmlspecialchars($company['industry']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $company['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete.php?id=<?= $company['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

<?php include '../../templates/footer.php'; ?>
