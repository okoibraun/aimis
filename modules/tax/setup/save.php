<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");

$id = $_POST['id'] ?? null;

$data = [
  'tax_type'   => $_POST['tax_type'],
  'rate'       => floatval($_POST['rate']),
  'description'=> $_POST['description'],
  'is_active'  => $_POST['is_active']
];

if ($id) {
  $stmt = $conn->prepare("UPDATE tax_config SET tax_type=?, rate=?, description=?, is_active=? WHERE id=? AND company_id=?");
  $stmt->bind_param('sdssii', $data['tax_type'], $data['rate'], $data['description'], $data['is_active'], $id, $company_id);
  
  if($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $company_id, $user_id, 'tax', 'TAX_UPDATED', 'tax_config', $id, "Tax type: {$_POST['tax_type']}, Rate: {$_POST['rate']}%");
    // Update successful
    $_SESSION['success'] = "Tax rule updated successfully.";
    header("Location: ./");
    exit;
  } else {
    // Update failed
    $_SESSION['error'] = "Failed to update tax rule: " . $stmt->error;
    header("Location: ./");
    exit;
  }
} else {
  $stmt = $conn->prepare("INSERT INTO tax_config (company_id, user_id, employee_id, tax_type, rate, description, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param('iiisssi', $company_id, $user_id, $employee_id, $data['tax_type'], $data['rate'], $data['description'], $data['is_active']);
  
  if($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $company_id, $user_id, 'tax', 'TAX_CREATED', 'tax_config', $conn->insert_id, "Tax type: {$_POST['tax_type']}, Rate: {$_POST['rate']}%");
    // Insert successful
    $_SESSION['success'] = "Tax rule added successfully.";
    header("Location: ./");
    exit;
  } else {
    // Insert failed
    $_SESSION['error'] = "Failed to add tax rule: " . $stmt->error;
    header("Location: ./");
    exit;
  }
}

