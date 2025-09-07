<?php
require_once '../../functions/invite_functions.php';
require_once '../../config/db.php';

$token = $_GET['token'] ?? '';
$invite = get_invite_by_token($token);
$error = '';
$success = '';

if (!$invite) {
    die("Invalid or expired token.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, name, is_active) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sss", $invite['email'], $password, $name);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        assign_role_to_user($user_id, $invite['company_id'], $invite['role_id']);
        mark_invite_accepted($invite['id']);
        $success = "Account created. You may now login.";
    } else {
        $error = "Failed to register.";
    }
}
?>

<?php include '../../templates/header.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Accept Invitation</h1>
    </section>
    <section class="content">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php else: ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Create Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Register</button>
            </form>
        <?php endif; ?>
    </section>
</div>

<?php include '../../templates/footer.php'; ?>
