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

$id = (int) ($_GET['id'] ?? 0);

// Fetch tag to get module for redirect
$stmt = $conn->prepare("SELECT * FROM crm_tags WHERE id = ? AND company_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();
$tag = $result->fetch_assoc();

if (!$tag) {
    die('Tag not found or access denied.');
}

// Delete tag
$del = $conn->prepare("DELETE FROM crm_tags WHERE id = ? AND company_id = ?");
$del->bind_param('ii', $id, $_SESSION['company_id']);
$del->execute();

header("Location: index.php?module=" . $tag['module']);
exit;
