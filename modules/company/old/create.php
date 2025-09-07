<?php
require_once '../../functions/auth_functions.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/role_functions.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $industry = trim($_POST['industry']);
    
    if ($name !== '') {
        $company_id = create_company($name, $industry);
        
        if ($company_id) {
            // Assuming '1' is the default admin role ID
            assign_role_to_user($user_id, $company_id, 1);
            $success = true;
        } else {
            $error = "Failed to create company.";
        }
    } else {
        $error = "Company name is required.";
    }
}

include '../../templates/header.php';
include '../../templates/navbar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Create Company</h1>
    </section>
    <section class="content">
        <?php if ($success): ?>
            <div class="alert alert-success">Company created successfully!</div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Industry</label>
                <input type="text" name="industry" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Create Company</button>
        </form>
    </section>
</div>

<?php include '../../templates/footer.php'; ?>
