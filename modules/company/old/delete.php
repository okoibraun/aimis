<?php
require_once '../../functions/auth_functions.php';
require_once '../../functions/company_functions.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit();
}

$company_id = $_GET['id'] ?? null;
if ($company_id) {
    delete_company($company_id);
}

header("Location: list.php");
exit();
