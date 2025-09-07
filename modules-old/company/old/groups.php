<?php
require_once '../../functions/auth_functions.php';
require_once '../../config/db.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = trim($_POST['group_name']);
    if ($group_name !== '') {
        $stmt = $conn->prepare("INSERT INTO company_groups (name) VALUES (?)");
        $stmt->bind_param("s", $group_name);
        if ($stmt->execute()) {
            $success = "Group created successfully.";
        } else {
            $error = "Failed to create group.";
        }
    } else {
        $error = "Group name is required.";
    }
}

// Fetch groups
$groups = $conn->query("SELECT * FROM company_groups");

include '../../templates/header.php';
include '../../templates/navbar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Company Groups</h1>
    </section>
    <section class="content">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-3">
            <div class="form-group">
                <label>New Group Name</label>
                <input type="text" name="group_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Group</button>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Group Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($group = $groups->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($group['name']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

<?php include '../../templates/footer.php'; ?>
