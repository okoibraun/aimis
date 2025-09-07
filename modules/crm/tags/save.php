<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$module = $_POST['module'] ?? '';
$name = trim($_POST['name'] ?? '');
$color = trim($_POST['color'] ?? '');

$allowed_modules = ['contact', 'company', 'deal'];
if (!in_array($module, $allowed_modules) || $name === '') {
    die('Invalid input.');
}

// Basic hex color validation
if ($color && !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
    $color = '#0073b7'; // default AdminLTE blue
}

$stmt = $conn->prepare("INSERT INTO crm_tags (name, module, color, company_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param('sssi', $name, $module, $color, $_SESSION['company_id']);

if ($stmt->execute()) {
    header("Location: index.php?module=$module");
    exit;
} else {
    die('Database error: ' . $conn->error);
}
