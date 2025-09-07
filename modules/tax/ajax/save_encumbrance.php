<?php
session_start();
// Include database connection and log function
include_once '../../../config/db.php';
include_once '../functions/log_event.php';

$id = $_POST['id'] ?? null;
$company_id = $_SESSION['company_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$data = [
  'fund_id'         => $_POST['fund_id'],
  'amount'          => floatval($_POST['amount']),
  'purpose'         => $_POST['purpose'],
  'encumbered_date' => $_POST['enc_date'],
  'released_date'   => $_POST['rel_date'] ?: null,
  'status'          => $_POST['status'],
];

if ($id) {
  $stmt = $conn->prepare("UPDATE tax_budget_encumbrance SET fund_id=?, amount=?, purpose=?, encumbered_date=?, released_date=?, status=? WHERE id=?");
  $stmt->bind_param("idssssi", $data['fund_id'], $data['amount'], $data['purpose'], $data['encumbered_date'], $data['released_date'], $data['status'], $id);
  
  if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'public_funds', 'ENCUMBRANCE_UPDATED', 'budget_encumbrance', $id, "Fund: {$_POST['fund_name']}, Amount: {$_POST['amount']}, Status: {$_POST['status']}");
    // Update successful
    $_SESSION['success'] = "Encumbrance updated successfully.";
    header("Location: ../budget.php");
    exit;
  } else {
    $_SESSION['error'] = "Error updating encumbrance: " . $stmt->error;
    header("Location: ../budget.php");
    $stmt->close();
    exit;
  }
} else {
  $stmt = $conn->prepare("INSERT INTO tax_budget_encumbrance (fund_id, amount, purpose, encumbered_date, released_date, status) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("idssss", $data['fund_id'], $data['amount'], $data['purpose'], $data['encumbered_date'], $data['released_date'], $data['status']);
  
  if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'public_funds', 'ENCUMBRANCE_CREATED', 'budget_encumbrance', $conn->insert_id, "Fund: {$_POST['fund_name']}, Amount: {$_POST['amount']}, Status: {$_POST['status']}");
    // Insert successful
    $_SESSION['success'] = "Encumbrance created successfully.";
    header("Location: ../budget.php");
    exit;
  } else {
    $_SESSION['error'] = "Error creating encumbrance: " . $stmt->error;
    header("Location: ../budget.php");
    $stmt->close();
    exit;
  }
}