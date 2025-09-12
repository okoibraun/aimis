<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");
include("../functions/log_event.php");

$id = $_POST['id'] ?? null;

$data = [
  'grant_name'     => $_POST['grant_name'],
  'source'         => $_POST['source'],
  'amount_awarded' => floatval($_POST['amount_awarded']),
  'amount_spent'   => floatval($_POST['amount_spent']),
  'start_date'     => $_POST['start_date'],
  'end_date'       => $_POST['end_date'],
  'status'         => $_POST['status']
];

if ($id) {
  $stmt = $conn->prepare("UPDATE tax_grants SET grant_name=?, source=?, amount_awarded=?, amount_spent=?, start_date=?, end_date=?, status=? WHERE id=? AND company_id=?");
  $stmt->bind_param("ssddsssii", $data['grant_name'], $data['source'], $data['amount_awarded'], $data['amount_spent'], $data['start_date'], $data['end_date'], $data['status'], $id, $company_id);
  if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'public_tax', 'UPDATED_GRANT', 'tax_grants', $id, "Grant: {$_POST['grant_name']}, Source: {$_POST['source']}, Amount Awarded: {$_POST['amount_awarded']}, Amount Spent: {$_POST['amount_spent']}");
    // Update successful
    $_SESSION['success'] = "Grant updated successfully.";
    header('Location: ./');
    exit;
  } else {
    $_SESSION['error'] = "Failed to update grant. {$stmt->error}";
    $stmt->close();
  }
} else {
  $stmt = $conn->prepare("INSERT INTO tax_grants (company_id, user_id, employee_id, grant_name, source, amount_awarded, amount_spent, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiissddsss", $company_id, $user_id, $employee_id, $data['grant_name'], $data['source'], $data['amount_awarded'], $data['amount_spent'], $data['start_date'], $data['end_date'], $data['status']);
  if($stmt->execute()) {
    // Log the event
    log_tax_event($conn, $company_id, $user_id, 'public_tax', 'CREATED_GRANT', 'tax_grants', $conn->insert_id, "Grant: {$_POST['grant_name']}, Source: {$_POST['source']}, Amount Awarded: {$_POST['amount_awarded']}, Amount Spent: {$_POST['amount_spent']}");
    // Insert successful
    $_SESSION['success'] = "Grant saved successfully.";
    header("Location: ./");
    exit;
  } else {
    $_SESSION['error'] = "Failed to save grant. {$stmt->error}";
    $stmt->close();
  }
}
