<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT related_type, related_id FROM crm_communications WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Not found");
}

$row = $result->fetch_assoc();

$del = $conn->prepare("DELETE FROM crm_communications WHERE id = ? AND company_id = ?");
$del->bind_param("ii", $id, $company_id);
$del->execute();

header("Location: list.php?related_type={$row['related_type']}&related_id={$row['related_id']}");
exit;
