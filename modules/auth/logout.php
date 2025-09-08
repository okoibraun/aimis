<?php
session_start();
require_once '../../functions/log_functions.php';
require_once '../../functions/helpers.php';

// Capture session data before destroying
$user_id = $_SESSION['user_id'] ?? null;
$company_id = $_SESSION['company_id'] ?? null;

if ($user_id && $company_id) {
    log_activity($user_id, $company_id, 'logout', 'User logged out');
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login
redirect('login.php');
