<?php
include_once '../../../config/db.php';

$id = $_GET['id'] ?? null;
$company_id = $_SESSION['company_id'] ?? 0; // Ensure company_id is set, default to 0 if not
if ($id) {
  $stmt = $pdo->prepare("DELETE FROM tax_config WHERE id = ? AND company_id = ?");
  $stmt->bind_param("ii", $id, $company_id);

  if ($stmt->execute()) {
    // Deletion successful
    $_SESSION['success'] = "Tax rule deleted successfully.";
    header("Location: ../setup.php");
    exit;
  } else {
    // Deletion failed
    $_SESSION['error'] = "Failed to delete tax rule: " . $stmt->error;
    header("Location: ../setup.php");
    exit;
  }
}
