<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");

$id = $_GET['id'] ?? null;

if ($id) {
  $stmt = $conn->prepare("DELETE FROM tax_config WHERE id = ? AND company_id = ?");
  $stmt->bind_param("ii", $id, $company_id);

  if ($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $company_id, $user_id, 'tax', 'TAX_DELETED', 'tax_config', $id, "Tax type: {$_GET['tax_type']}, Rate: {$_GET['rate']}%");
    
    // Deletion successful
    $_SESSION['success'] = "Tax rule deleted successfully.";
    header("Location: ./");
    exit;
  } else {
    // Deletion failed
    $_SESSION['error'] = "Failed to delete tax rule: " . $stmt->error;
    header("Location: ./");
    exit;
  }
}
