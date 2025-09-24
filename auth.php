<?php
session_start();
require_once('config/db.php'); // Your database connection
include("./functions/helpers.php");

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND status = 'active' LIMIT 1");

if (mysqli_num_rows($query) == 1) {
    $user = mysqli_fetch_assoc($query);

    if (password_verify($password, $user['password'])) {
        // Password correct, set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['employee_id'] = $user['employee_id'];
        $_SESSION['company_id'] = $user['company_id'];
        if($user['company_id'] != 0) $_SESSION['company_logo'] = $conn->query("SELECT logo FROM companies WHERE id = '{$user['company_id']}'")->fetch_assoc()['logo'];
        $_SESSION['user_department'] = $user['department'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar'];

        // Update last login
        $now = date('Y-m-d H:i:s');
        $conn->query("UPDATE users SET last_login = '$now' WHERE id = {$user['id']}");

        // Log login
        require_once './functions/log_functions.php';
        log_activity($user['id'], $user['company_id'], 'login', 'User logged in');

        if($user['company_id'] == 0 || $user['company_id'] == null || $user['company_id'] == '') {
            // Redirect to company selection if no company assigned
            header('Location: onboard');
            exit();
        }
        
        header('Location: ./');
        exit();
    } else {
        $_SESSION['error'] = "Invalid login credentials.";
        header('Location: login');
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid login credentials.";
    header('Location: login');
    exit();
}
