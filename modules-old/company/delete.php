<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/company_functions.php';
require_once '../../functions/auth_functions.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    redirect('../../login.php');
}

$company_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$company = get_company_by_id($company_id);

if (!$company || !user_can_manage_company($_SESSION, $company)) {
    die("Unauthorized or company not found.");
}

if (delete_company($company_id)) {
    redirect('index.php');
} else {
    die("Failed to delete company.");
}
