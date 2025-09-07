<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

// $company_id = get_current_company_id();
$company_id = $_SESSION['company_id'];
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$data = [
  'lead_id'     => $_POST['lead_id'],
  'full_name'   => $_POST['full_name'],
  'email'       => $_POST['email'],
  'phone'       => $_POST['phone'],
  'position'   => $_POST['position'],
  'company_id'  => $_SESSION['company_id'],
  'notes'       => $_POST['notes'],
  'crm_company_id' => !empty($_POST['crm_company_id']) ? intval($_POST['crm_company_id']) : null
];

if ($id > 0) {
  $stmt = $conn->prepare("UPDATE crm_contacts SET lead_id=?, full_name=?, email=?, phone=?, position=?, company_id=?, notes=?, crm_company_id=? WHERE id=? AND company_id=?");
  $stmt->bind_param("issssssiii", $data['lead_id'], $data['full_name'], $data['email'], $data['phone'], $data['position'], $data['company_id'], $data['notes'], $data['crm_company_id'], $id, $company_id);
} else {
  $stmt = $conn->prepare("INSERT INTO crm_contacts (lead_id, company_id, full_name, email, phone, position, crm_company_id, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iissssis", $data['lead_id'], $company_id, $data['full_name'], $data['email'], $data['phone'], $data['position'], $data['crm_company_id'], $data['notes']);
}

$stmt->execute();
header("Location: list.php");
exit;
