<?php
require_once '../../functions/auth_functions.php';
require_once '../../functions/invite_functions.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $_GET['company_id'] ?? null;
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];

    if (send_invitation($email, $company_id, $role_id, $user_id)) {
        $success = "Invitation sent successfully.";
    } else {
        $error = "Failed to send invitation.";
    }
}

include '../../templates/header.php';
include '../../templates/navbar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Invite User</h1>
    </section>
    <section class="content">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="company_id" value="<?= $company_id ?>">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Role ID</label>
                <input type="number" name="role_id" class="form-control" required>
                <!-- In production: use dropdown from roles table -->
            </div>
            <button type="submit" class="btn btn-primary">Send Invitation</button>
        </form>
    </section>
</div>

<?php include '../../templates/footer.php'; ?>
