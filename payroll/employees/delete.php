<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

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

$emp_id = $_GET['emp_id'];

$company_id = $_SESSION['company_id'];

if(isset($_GET['a'], $_GET['t'], $_GET['id']) && $_GET['t'] == "account") {
    $dlete_stmt = $conn->query("DELETE FROM bank_accounts WHERE company_id = $company_id AND employee_id = $emp_id AND id = '{$_GET['id']}'");
    if($dlete_stmt) {
        header("Location: edit_employee.php?id={$emp_id}");
    }
}

if(isset($_GET['a'], $_GET['t'], $_GET['id']) && $_GET['t'] == "bonus") {
    $dlete_stmt = $conn->query("DELETE FROM employee_bonuses WHERE company_id = $company_id AND employee_id = $emp_id AND id = '{$_GET['id']}'");
    if($dlete_stmt) {
        header("Location: edit_employee.php?id={$emp_id}");
    }
}

if(isset($_GET['a'], $_GET['t'], $_GET['id']) && $_GET['t'] == "deduction") {
    $dlete_stmt = $conn->query("DELETE FROM employee_deductions WHERE company_id = $company_id AND employee_id = $emp_id AND id = '{$_GET['id']}'");
    if($dlete_stmt) {
        header("Location: edit_employee.php?id={$emp_id}");
    }
}

if(isset($_GET['a'], $_GET['t'], $_GET['id']) && $_GET['t'] == "allowance") {
    $dlete_stmt = $conn->query("DELETE FROM employee_allowances WHERE company_id = $company_id AND employee_id = $emp_id AND id = '{$_GET['id']}'");
    if($dlete_stmt) {
        header("Location: edit_employee.php?id={$emp_id}");
    }
}
?>
