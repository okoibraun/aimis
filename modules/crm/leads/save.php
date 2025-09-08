<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();
$assigned_to = get_current_user_id(); // default assigned user

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$data = [
  'full_name' => $_POST['full_name'],
  'email' => $_POST['email'],
  'phone' => $_POST['phone'],
  'job_title' => $_POST['job_title'],
  'company_name' => $_POST['company_name'],
  'source' => $_POST['source'],
  'status' => $_POST['status'],
  'notes' => $_POST['notes'],
];

if ($id > 0) {
  $stmt = $conn->prepare("UPDATE crm_leads SET full_name=?, email=?, phone=?, job_title=?, company_name=?, source=?, status=?, notes=? WHERE id=? AND company_id=?");
  $stmt->bind_param("ssssssssii", $data['full_name'], $data['email'], $data['phone'], $data['job_title'], $data['company_name'], $data['source'], $data['status'], $data['notes'], $id, $company_id);
} else {
  $stmt = $conn->prepare("INSERT INTO crm_leads (company_id, full_name, email, phone, job_title, company_name, source, status, assigned_to, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssssssis", $company_id, $data['full_name'], $data['email'], $data['phone'], $data['job_title'], $data['company_name'], $data['source'], $data['status'], $assigned_to, $data['notes']);
}

$stmt->execute();

if (!empty($_POST['doc_ids'])) {
    $conn->prepare("DELETE FROM crm_document_links WHERE crm_module = 'lead' AND module_ref_id = ?")->execute([$lead_id]);

    foreach ($_POST['doc_ids'] as $doc_id) {
        $conn->prepare("INSERT INTO crm_document_links (document_id, crm_module, module_ref_id, linked_by)
                       VALUES (?, 'lead', ?, ?)")->execute([$doc_id, $lead_id, $_SESSION['user_id']]);
    }
}

header("Location: list.php");
exit;
