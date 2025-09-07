<?php
session_start();
require_once '../../config/db.php';

$doc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if document exists
$doc = $conn->query("SELECT * FROM documents WHERE id = $doc_id")->fetch_assoc();

if (!$doc) {
    die("Document not found.");
}

// OPTIONAL: check user permission (admin only)
$user_role = "admin" ?? "superadmin";
if ($_SESSION['user_role'] !== $user_role) {
    die("Unauthorized access.");
}

// Update status
$conn->query("UPDATE documents SET status = 'approved' WHERE id = $doc_id");

header("Location: view.php?id=$doc_id&approved=1");
exit;
