<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
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
  $stmt = $conn->prepare("DELETE FROM intl_tax_rules WHERE id = ? AND company_id = ?");
  $stmt->bind_param("ii", $id, $company_id);
  if($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $company_id, $user_id, 'tax', 'DELETED_RULE', 'tax_rules', $id, "Rule - ID #: $id");
    $_SESSION['success'] = "Rule deleted successfully.";
    header("Location: ./");
    exit;
  } else {
    $_SESSION['error'] = "Failed to delete rule.";
    header("Location: ./");
    exit;
  }
}


