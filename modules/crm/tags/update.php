<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$id = (int) ($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$color = trim($_POST['color'] ?? '');

if ($name === '') {
    die('Tag name is required.');
}

// Fetch existing tag
$stmt = $conn->prepare("SELECT * FROM crm_tags WHERE id = ? AND company_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();
$tag = $result->fetch_assoc();

if (!$tag) {
    die('Tag not found.');
}

// Validate color hex
if ($color && !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
    $color = '#0073b7'; // fallback
}

$stmt = $conn->prepare("UPDATE crm_tags SET name = ?, color = ? WHERE id = ? AND company_id = ?");
$stmt->bind_param('ssii', $name, $color, $id, $_SESSION['company_id']);

if ($stmt->execute()) {
    header("Location: index.php?module=" . $tag['module']);
    exit;
} else {
    die('Database error: ' . $conn->error);
}
