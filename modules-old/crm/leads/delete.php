<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$id = intval($_GET['id']);
$company_id = get_current_company_id();

$stmt = $conn->prepare("DELETE FROM crm_leads WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();

header("Location: list.php");
exit;
