<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
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

$id = intval($_GET['id']);
$company_id = get_current_company_id();
$user_id = get_current_user_id();

$stmt = $conn->prepare("DELETE FROM crm_reminders WHERE id = ? AND company_id = ? AND user_id = ?");
$stmt->bind_param("iii", $id, $company_id, $user_id);
$stmt->execute();

header("Location: list.php");
exit;
