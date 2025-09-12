<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($user_id)) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "delete";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'];
$delete = $conn->query("DELETE FROM production_resources WHERE id = $id AND company_id = $company_id");
if($delete) header("Location: ./");
exit;