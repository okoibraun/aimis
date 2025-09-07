<?php
session_start();
include('../config/db.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'"));

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $user_id = $user['id'];

        // mysqli_query($conn, "UPDATE users SET token = '$token', reset_requested_at = NOW() WHERE id = $user_id");
        $conn->query("UPDATE users SET token = '$token' WHERE id = $user_id");

        // In production, send by EMAIL
        $reset_link = "http://aimiscloud.com.ng/reset_password_user.php?token=$token";

        $message = "Reset link (Simulated): <a href='$reset_link'>Reset Password</a>";
    } else {
        $message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Memo System</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2>Forgot Password</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group mb-3">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Request Reset Link</button>
    </form>

</div>

</body>
</html>