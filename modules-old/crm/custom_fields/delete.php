<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$id = (int) ($_GET['id'] ?? 0);

// Fetch the field to confirm ownership and get module
$stmt = $conn->prepare("SELECT * FROM crm_custom_field_definitions WHERE id = ? AND company_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();
$field = $result->fetch_assoc();

if (!$field) {
    die('Custom field not found or access denied.');
}

$module = $field['module'];

// Delete field
$del = $conn->prepare("DELETE FROM crm_custom_field_definitions WHERE id = ? AND company_id = ?");
$del->bind_param('ii', $id, $_SESSION['company_id']);
$del->execute();

header("Location: index.php?module=$module");
exit;
