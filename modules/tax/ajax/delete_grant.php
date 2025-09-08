<?php
include_once '../../../config/db.php';

$id = $_GET['id'] ?? null;
$company_id = $_SESSION['company_id'];

if ($id) {
  $stmt = $conn->prepare("DELETE FROM tax_grants WHERE id = ? AND company_id = ?");
  $stmt->bind_param("ii", $id, $company_id);
  if($stmt->execute()) {
    $_SESSION['success'] = "Grant deleted successfully.";
    header("Location: ../grants.php");
    exit;
  } else {
    $_SESSION['error'] = "Failed to delete grant. {$stmt->error}";
    $stmt->close();
    header("Location: ../grants.php");
    exit;
  }
}

