<?php
session_start();
// Include database connection and header
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
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

if(in_array($_SESSION['user_role'], system_users())) {
    $query = "DELETE FROM folders WHERE id = $id";
} else if(in_array($_SESSION['user_role'], super_roles())) {
    $query = "DELETE FROM folders WHERE id = $id AND company_id = $company_id";
} else {
    $query = "DELETE FROM folders WHERE id = $id AND company_id = $company_id AND created_by = $user_id";
}

if (mysqli_query($conn, $query)) {
    // Log Audit
    include_once('../../includes/audit_log.php');
    include_once('../../functions/log_functions.php');
    log_activity($user_id, $company_id, 'delete_folder', "Deleted Folder: ". $conn->query("SELECT name FROM folders WHERE id = $id")->fetch_assoc()['name']);

    $_SESSION['success'] = "Folder deleted successfully.";
    // Redirect to the folders index page
    header('Location: ./');
    exit();
} else {
    $_SESSION['error'] = "Error deleting folder: " . mysqli_error($conn);
}
?>