<?php
include_once '../../../config/db.php';

$id = $_GET['id'] ?? null;
$company_id = $_SESSION['company_id'] ?? 0; // Ensure company_id is set

if ($id) {
  $stmt = $conn->prepare("DELETE FROM intl_tax_rules WHERE id = ? AND company_id = ?");
  $stmt->bind_param("ii", $id, $company_id);
  if($stmt->execute()) {
    $_SESSION['success'] = "Rule deleted successfully.";
    header("Location: ../rules.php");
    exit;
  } else {
    $_SESSION['error'] = "Failed to delete rule.";
    header("Location: ../rules.php");
    exit;
  }
}


