<?php
session_start();
include('../config/db.php');

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'system') {
    header('Location: ../login.php');
    exit();
}

$id = intval($_GET['id']);
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $id"));

if (!$user) {
    header('Location: index.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    //Check if Password is strong enough
    function is_strong_password($password) {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password);
    }
    
    // Example usage before reset password:
    if (!is_strong_password($new_password)) {
        $message = "Password must be at least 8 characters, include upper/lowercase letters and a number.";
    } else {
        // hash and update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
        $update = mysqli_query($conn, "UPDATE users SET password='$hashed_password' WHERE id=$id");
    
        if ($update) {
            $message = "Password reset successfully!";
            //Log Audit
            include_once('../includes/audit_log.php');
            log_audit($conn, $_SESSION['user_id'], 'Reset Password', 'Reset password for user ID: '.$id);
        } else {
            $message = "Failed to reset password.";
        }
    }
    
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Memo System</title>
    <link rel="stylesheet" href="../assets/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2>Reset Password for <?php echo htmlspecialchars($user['name']); ?></h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 characters" required minlength="8">
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>