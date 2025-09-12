<?php
session_start();
// Include database connection and log function
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");
include_once '../functions/log_event.php';

$id = $_POST['id'] ?? null;

$data = [
  'fund_id'         => $_POST['fund_id'],
  'amount'          => floatval($_POST['amount']),
  'purpose'         => $_POST['purpose'],
  'encumbered_date' => $_POST['enc_date'],
  'released_date'   => $_POST['rel_date'] ?: null,
  'status'          => $_POST['status'],
];

if ($id) {
  $stmt = $conn->prepare("UPDATE tax_budget_encumbrance SET fund_id=?, amount=?, purpose=?, encumbered_date=?, released_date=?, status=? WHERE id=? AND company_id=?");
  $stmt->bind_param("idssssii", $data['fund_id'], $data['amount'], $data['purpose'], $data['encumbered_date'], $data['released_date'], $data['status'], $id, $company_id);
  
  if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'public_tax', 'UPDATED_ENCUMBRAANCE', 'tax_encumbrance', $id, "Fund: {$_POST['fund_name']}, Amount: {$_POST['amount']}, Status: {$_POST['status']}");
    // Update successful
    $_SESSION['success'] = "Encumbrance updated successfully.";
    header("Location: ./");
    exit;
  } else {
    $_SESSION['error'] = "Error updating encumbrance: " . $stmt->error;
    header("Location: ./");
    $stmt->close();
    exit;
  }
} else {
  $stmt = $conn->prepare("INSERT INTO tax_budget_encumbrance (company_id, user_id, employee_id, fund_id, amount, purpose, encumbered_date, released_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiiidssss", $company_id, $user_id, $employee_id, $data['fund_id'], $data['amount'], $data['purpose'], $data['encumbered_date'], $data['released_date'], $data['status']);
  
  if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'public_tax', 'CREATED_ENCUMBRANCE', 'tax_encumbrance', $conn->insert_id, "Fund: {$_POST['fund_name']}, Amount: {$_POST['amount']}, Status: {$_POST['status']}");
    // Insert successful
    $_SESSION['success'] = "Encumbrance created successfully.";
    header("Location: ./");
    exit;
  } else {
    $_SESSION['error'] = "Error creating encumbrance: " . $stmt->error;
    header("Location: ./");
    $stmt->close();
    exit;
  }
}