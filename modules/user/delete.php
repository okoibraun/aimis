<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/user_functions.php';
require_once '../../functions/company_functions.php';
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Check User Permissions
$page = "delete";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
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
