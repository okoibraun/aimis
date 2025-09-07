<?php
include_once '../../../config/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
  $stmt = $conn->prepare("DELETE FROM tax_budget_encumbrance WHERE id = ?");
  $stmt->bind_param("i", $id);
  
  if ($stmt->execute()) {
    $_SESSION['success'] = "Encumbrance deleted successfully.";
    header("Location: ../budget.php");
    exit;
  } else {
    $_SESSION['error'] = "Error deleting encumbrance: " . $stmt->error;
    header("Location: ../budget.php");
    exit;
  }
}
