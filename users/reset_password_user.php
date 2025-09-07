<?php
session_start();
include('../config/db.php');

$token = mysqli_real_escape_string($conn, $_GET['token']);
//$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE reset_token = '$token'"));
$user = $conn->query("SELECT * FROM users WHERE token = '$token'")->fetch_assoc();

if (!$user) {
    die('Invalid or expired token.');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // mysqli_query($conn, "UPDATE users SET password='$hashed_password', reset_token=NULL, reset_requested_at=NULL WHERE id=" . $user['id']);
    $conn->query("UPDATE users SET password='$hashed_password', token=NULL, WHERE id = '{$user['id']}'");

    $message = "Password has been reset! You can now <a href='login.php'>login</a>.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Memo System</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2>Reset Your Password</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary">Change Password</button>
    </form>

</div>

</body>
</html>