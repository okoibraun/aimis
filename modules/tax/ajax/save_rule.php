<?php
include_once '../../../config/db.php';

$id = $_POST['id'] ?? null;
$data = [
  'country'       => $_POST['country'],
  'template_name' => $_POST['template_name'],
  'beps_compliant'=> $_POST['beps_compliant'],
  'is_active'     => $_POST['is_active'],
  'company_id'    => $_SESSION['company_id']
];

if ($id) {
  $stmt = $conn->prepare("UPDATE intl_tax_rules SET country=?, template_name=?, beps_compliant=?, is_active=?, updated_at=NOW() WHERE id=? AND company_id=?");
  $stmt->bind_param("ssiiii", $data['country'], $data['template_name'], $data['beps_compliant'], $data['is_active'], $id, $data['company_id']);

  if($stmt->execute()) {
    $_SESSION['success'] = "Rule updated successfully.";
    header("Location: ../rules.php");
    exit;
  } else {
    $_SESSION['error'] = "Failed to update rule.";
    header("Location: ../rules.php");
    exit;
  }
  $stmt->close();
  
} else {
  $stmt = $pdo->prepare("INSERT INTO intl_tax_rules (company_id, country, template_name, beps_compliant, is_active) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issii", $data['company_id'], $data['country'], $data['template_name'], $data['beps_compliant'], $data['is_active']);

  if($stmt->execute()) {
    $_SESSION['success'] = "Rule created successfully.";
    header("Location: ../rules.php");
    exit;
  } else {
    $_SESSION['error'] = "Failed to create rule.";
    header("Location: ../rules.php");
    exit;
  }
  $stmt->close();
}
