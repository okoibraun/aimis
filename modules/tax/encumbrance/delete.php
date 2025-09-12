<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "delete";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
  $stmt = $conn->prepare("DELETE FROM tax_budget_encumbrance WHERE id = ? AND company_id = $company_id");
  $stmt->bind_param("i", $id);
  
  if ($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $company_id, $user_id, 'public_tax', 'DELETED_ENCUMBRANCE', 'tax_encumbrance', $id, "Encubrance ID: #{$id}");
    // Delete Successful
    $_SESSION['success'] = "Encumbrance deleted successfully.";
    header("Location: ./");
    exit;
  } else {
    $_SESSION['error'] = "Error deleting encumbrance: " . $stmt->error;
    header("Location: ./");
    exit;
  }
}
