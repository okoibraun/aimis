<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");

$id = $_POST['id'] ?? null;
$data = [
  'country'       => $_POST['country'],
  'template_name' => $_POST['template_name'],
  'beps_compliant'=> $_POST['beps_compliant'],
  'is_active'     => $_POST['is_active']
];

if ($id) {
  $stmt = $conn->prepare("UPDATE intl_tax_rules SET country=?, template_name=?, beps_compliant=?, is_active=?, updated_at=NOW() WHERE id=? AND company_id=?");
  $stmt->bind_param("ssiiii", $data['country'], $data['template_name'], $data['beps_compliant'], $data['is_active'], $id, $company_id);

  if($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $company_id, $user_id, 'tax', 'UPDATED_RULE', 'tax_rules', $id, "Rule - Country: {$_POST['country']}, Template: {$_POST['template_name']}");
    // Update successful
    $_SESSION['success'] = "Rule updated successfully.";
    header("Location: ./");
    exit;
  } else {
    $_SESSION['error'] = "Failed to update rule.";
    header("Location: ./");
    exit;
  }
  $stmt->close();
  
} else {
  $stmt = $conn->prepare("INSERT INTO intl_tax_rules (company_id, user_id, employee_id, country, template_name, beps_compliant, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiissii", $company_id, $user_id, $employee_id, $data['country'], $data['template_name'], $data['beps_compliant'], $data['is_active']);

  if($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $company_id, $user_id, 'tax', 'CREATED_RULE', 'tax_rules', $id, "Rule - Country: {$_POST['country']}, Template: {$_POST['template_name']}");
    $_SESSION['success'] = "Rule created successfully.";
    header("Location: ./");
    exit;
  } else {
    $_SESSION['error'] = "Failed to create rule.";
    header("Location: ./");
    exit;
  }
  $stmt->close();
}
