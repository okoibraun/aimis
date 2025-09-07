<?php
require_once '../../functions/auth_functions.php';
require_once '../../functions/company_functions.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit();
}

$company_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$company_id) {
    die("Company ID required.");
}

$company = get_company($company_id);
if (!$company) {
    die("Company not found.");
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $industry = $_POST['industry'];
    
    if ($name !== '') {
        if (update_company($company_id, $name, $industry)) {
            $success = true;
            $company = get_company($company_id);
        } else {
            $error = "Failed to update company.";
        }
    }
}

include '../../templates/header.php';
include '../../templates/navbar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Company</h1>
    </section>
    <section class="content">
        <?php if ($success): ?>
            <div class="alert alert-success">Company updated.</div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($company['name']) ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Industry</label>
                <input type="text" name="industry" value="<?= htmlspecialchars($company['industry']) ?>" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update Company</button>
        </form>
    </section>
</div>

<?php include '../../templates/footer.php'; ?>
