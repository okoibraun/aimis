<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));

    if (password_verify($current_password, $user['password'])) {
        if (preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$hashed_password' WHERE id=$user_id");

            include_once('../includes/audit_log.php');
            log_audit($conn, $user_id, 'Password Change', 'Changed own password.');

            $_SESSION['success'] = "Password changed successfully.";
        } else {
            $_SESSION['error'] = "Password does not meet strength requirements.";
        }
    } else {
        $_SESSION['error'] = "Current password is incorrect.";
    }
}

header('Location: profile.php');
exit();
