<?php
session_start();
include_once '../../../config/db.php';

$id = $_POST['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$data = [
  'tax_type'   => $_POST['tax_type'],
  'rate'       => floatval($_POST['rate']),
  'description'=> $_POST['description'],
  'is_active'  => $_POST['is_active'],
  'company_id' => $_SESSION['company_id']
];

if ($id) {
  $stmt = $conn->prepare("UPDATE tax_config SET tax_type=?, rate=?, description=?, is_active=? WHERE id=? AND company_id=?");
  $stmt->bind_param('sdssii', $data['tax_type'], $data['rate'], $data['description'], $data['is_active'], $id, $data['company_id']);
  
  if($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $data['company_id'], $user_id, 'tax', 'TAX_UPDATED', 'tax_config', $id ?? $conn->insert_id, "Tax type: {$_POST['tax_type']}, Rate: {$_POST['rate']}%");
    // Update successful
    $_SESSION['success'] = "Tax rule updated successfully.";
    header("Location: ../setup.php");
    exit;
  } else {
    // Update failed
    $_SESSION['error'] = "Failed to update tax rule: " . $stmt->error;
    header("Location: ../setup.php");
    exit;
  }
} else {
  $stmt = $conn->prepare("INSERT INTO tax_config (company_id, tax_type, rate, description, is_active) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param('isssi', $data['company_id'], $data['tax_type'], $data['rate'], $data['description'], $data['is_active']);
  
  if($stmt->execute()) {
    //Log the event
    include_once '../functions/log_event.php';
    log_tax_event($conn, $data['company_id'], $user_id, 'tax', 'tax', 'TAX_CREATED', 'tax_config', $id ?? $conn->insert_id, "Tax type: {$_POST['tax_type']}, Rate: {$_POST['rate']}%");
    // Insert successful
    $_SESSION['success'] = "Tax rule added successfully.";
    header("Location: ../setup.php");
    exit;
  } else {
    // Insert failed
    $_SESSION['error'] = "Failed to add tax rule: " . $stmt->error;
    header("Location: ../setup.php");
    exit;
  }
}

