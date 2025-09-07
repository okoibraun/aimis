<?php
include_once '../../../config/db.php';

$id = $_POST['id'] ?? null;

$data = [
  'grant_name'     => $_POST['grant_name'],
  'source'         => $_POST['source'],
  'amount_awarded' => floatval($_POST['amount_awarded']),
  'amount_spent'   => floatval($_POST['amount_spent']),
  'start_date'     => $_POST['start_date'],
  'end_date'       => $_POST['end_date'],
  'status'         => $_POST['status'],
  'company_id'     => $_SESSION['company_id']
];

if ($id) {
  $stmt = $conn->prepare("UPDATE tax_grants SET grant_name=?, source=?, amount_awarded=?, amount_spent=?, start_date=?, end_date=?, status=? WHERE id=? AND company_id=?");
  $stmt->bind_param("ssddsssii", $data['grant_name'], $data['source'], $data['amount_awarded'], $data['amount_spent'], $data['start_date'], $data['end_date'], $data['status'], $id, $data['company_id']);
  if($stmt->execute()) {
    $_SESSION['success'] = "Grant updated successfully.";
    header('Location: ../grants.php');
    exit;
  } else {
    $_SESSION['error'] = "Failed to update grant. {$stmt->error}";
    $stmt->close();
  }
} else {
  $stmt = $pdo->prepare("INSERT INTO tax_grants (company_id, grant_name, source, amount_awarded, amount_spent, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issddsss", $data['company_id'], $data['grant_name'], $data['source'], $data['amount_awarded'], $data['amount_spent'], $data['start_date'], $data['end_date'], $data['status']);
  if($stmt->execute()) {
    $_SESSION['success'] = "Grant saved successfully.";
    header("Location: ../grants.php");
    exit;
  } else {
    $_SESSION['error'] = "Failed to save grant. {$stmt->error}";
    $stmt->close();
  }
}
