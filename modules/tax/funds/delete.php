<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");
include_once '../functions/log_event.php';

// Check User Permissions
$page = "delete";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
  $delete = $conn->query("DELETE FROM tax_funds WHERE id=$id AND company_id = $company_id");

  if ($delete) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'public_tax', 'DELETED_FUNDS', 'tax_funds', $id, "Fund: Code: {$_GET['fund_code']}");
    // Insert successful
    $_SESSION['success'] = "Fund deleted successfully.";
    header('Location: ./');
    exit;
  } else {
    $_SESSION['error'] = "Failed to delete fund.";
    header('Location: ./');
    exit;
  }
}

