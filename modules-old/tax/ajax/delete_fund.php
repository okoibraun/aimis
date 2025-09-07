<?php
include_once '../../../config/db.php';

$id = $_GET['id'] ?? null;
$company_id = $_SESSION['company_id'];
if ($id) {
  $delete = $conn->query("DELETE FROM tax_funds WHERE id=$id AND company_id = $company_id");

  if ($delete) {
    $_SESSION['success'] = "Fund deleted successfully.";
    header('Location: ../funds.php');
    exit;
  } else {
    $_SESSION['error'] = "Failed to delete fund.";
    header('Location: ../funds.php');
    exit;
  }
}

