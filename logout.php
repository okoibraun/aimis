<?php
session_start();
require_once './config/db.php';
require_once './functions/log_functions.php';
require_once './functions/helpers.php';

// Capture session data before destroying
$user_id = $_SESSION['user_id'] ?? null;
$company_id = $_SESSION['company_id'];

if ($user_id || $company_id) {
    // Log action to audit trail
    log_activity($user_id, $company_id, 'logout', 'User logged out');
}

// Unset and destroy the session
session_unset();
session_destroy();

// Redirect to login page
header('Location: login');

exit();
