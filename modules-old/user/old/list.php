<?php
require_once '../../functions/auth_functions.php';
require_once '../../config/db.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit();
}

$company_id = $_GET['company_id'] ?? null;

$stmt = $conn->prepare("SELECT u.id, u.name, u.email, r.name AS role 
                        FROM users u
                        JOIN user_company_roles ucr ON u.id = ucr.user_id
                        JOIN roles r ON r.id = ucr.role_id
                        WHERE ucr.company_id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$users = $stmt->get_result();

include '../../templates/header.php';
include '../../templates/navbar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Company Users</h1>
    </section>
    <section class="content">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

<?php include '../../templates/footer.php'; ?>
