<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

// Input validation
$module = $_POST['module'] ?? '';
$name = trim($_POST['name'] ?? '');
$field_type = $_POST['field_type'] ?? '';
$options = trim($_POST['options'] ?? '');

$allowed_modules = ['contact', 'company', 'deal'];
$allowed_types = ['text', 'number', 'date', 'select'];

if (!in_array($module, $allowed_modules) || !in_array($field_type, $allowed_types) || $name == '') {
    die('Invalid input.');
}

// Prepare options for "select" fields
$options = ($field_type === 'select') ? $options : null;

// Insert into DB
$stmt = $conn->prepare("
    INSERT INTO crm_custom_field_definitions (module, name, field_type, options, company_id)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param('ssssi', $module, $name, $field_type, $options, $_SESSION['company_id']);

if ($stmt->execute()) {
    header("Location: index.php?module=$module");
    exit;
} else {
    die('Database error: ' . $conn->error);
}
