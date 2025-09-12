<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");
include("../functions/log_event.php");

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
  $stmt = $conn->prepare("DELETE FROM tax_grants WHERE id = ? AND company_id = ?");
  $stmt->bind_param("ii", $id, $company_id);
  if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'public_tax', 'DELETED_GRANT', 'tax_grants', $id, "Grant: {$_GET['grant_name']}, ID #: $id");
    $_SESSION['success'] = "Grant deleted successfully.";
    header("Location: ./");
    exit;
  } else {
    $_SESSION['error'] = "Failed to delete grant. {$stmt->error}";
    $stmt->close();
    header("Location: ./");
    exit;
  }
}

