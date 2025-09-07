<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$id = (int) ($_GET['id'] ?? 0);

// Fetch tag to get module for redirect
$stmt = $conn->prepare("SELECT * FROM crm_tags WHERE id = ? AND company_id = ?");
$stmt->bind_param('ii', $id, $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();
$tag = $result->fetch_assoc();

if (!$tag) {
    die('Tag not found or access denied.');
}

// Delete tag
$del = $conn->prepare("DELETE FROM crm_tags WHERE id = ? AND company_id = ?");
$del->bind_param('ii', $id, $_SESSION['company_id']);
$del->execute();

header("Location: index.php?module=" . $tag['module']);
exit;
