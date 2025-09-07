<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/company_functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin', 'system'])) {
    redirect('../../login.php');
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = get_user_by_id($user_id);

if (!$user || !user_can_manage_company($_SESSION, ['id' => $user['company_id']])) {
    die('Unauthorized or user not found.');
}

if (delete_user($user_id)) {
    redirect('list.php');
} else {
    die('Failed to delete user.');
}
