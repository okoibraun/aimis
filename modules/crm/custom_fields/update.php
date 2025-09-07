<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$id = (int) ($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$options = trim($_POST['options'] ?? '');

// Get existing field
$stmt = $conn->prepare("SELECT * FROM crm_custom_field_definitions WHERE id = ? AND company_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();
$field = $result->fetch_assoc();

if (!$field) {
    die('Custom field not found.');
}

$module = $field['module'];
$field_type = $field['field_type'];

if ($name === '') {
    die('Field name is required.');
}

// Handle update
if ($field_type === 'select') {
    $stmt = $conn->prepare("UPDATE crm_custom_field_definitions SET name = ?, options = ? WHERE id = ? AND company_id = ?");
    $stmt->bind_param('ssii', $name, $options, $id, $_SESSION['company_id']);
} else {
    $stmt = $conn->prepare("UPDATE crm_custom_field_definitions SET name = ?, options = NULL WHERE id = ? AND company_id = ?");
    $stmt->bind_param('sii', $name, $id, $_SESSION['company_id']);
}

if ($stmt->execute()) {
    header("Location: index.php?module=$module");
    exit;
} else {
    die('Update failed: ' . $conn->error);
}
