<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();
$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM crm_companies WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();

header("Location: list.php");
exit;
