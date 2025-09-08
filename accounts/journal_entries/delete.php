<?php
session_start();
include '../../config/db.php';
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
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

if($_SERVER['REQUEST_METHOD'] == "GET") {
    // Delete lines first (FK constraint)
    $conn->query("DELETE FROM journal_lines WHERE journal_entry_id = $id");
    
    // Delete entry
    $conn->query("DELETE FROM journal_entries WHERE company_id = $company_id AND id = $id");
    
    header("Location: ./");
    exit();
}

?>
