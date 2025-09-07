<?php
require_once '../../../../config/db.php';
require_once '../../../../functions/auth_functions.php';
require_once '../../../../functions/role_functions.php';


$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$data = [
  'full_name' => $_POST['name'],
  'lead_title' => $_POST['title'],
  'email' => $_POST['email'],
  'phone' => $_POST['phone'],
  'job_title' => $_POST['job_title'],
  'company_name' => $_POST['company_name'],
  'source' => $_POST['source'],
  'status' => $_POST['status'],
  'notes' => $_POST['notes'],
  'customer_type' => $_POST['customer_type'],
  'assigned_to' => $_POST['assigned_to'] ?? $user_id
];

if ($id > 0) {
  $stmt = $conn->prepare("UPDATE sales_customers SET title=?, name=?, email=?, phone=?, job_title=?, company_name=?, source=?, status=?, notes=?, assigned_to=? WHERE id=? AND company_id=?");
  $stmt->bind_param("ssssssssssii", $data['lead_title'], $data['full_name'], $data['email'], $data['phone'], $data['job_title'], $data['company_name'], $data['source'], $data['status'], $data['notes'], $data['assigned_to'], $id, $company_id);
} else {
  $stmt = $conn->prepare("INSERT INTO sales_customers (company_id, user_id, employee_id, title, name, email, phone, job_title, company_name, source, status, assigned_to, notes, customer_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiissssssssiss", $company_id, $user_id, $employee_id, $data['lead_title'], $data['full_name'], $data['email'], $data['phone'], $data['job_title'], $data['company_name'], $data['source'], $data['status'], $user_id, $data['notes'], $data['customer_type']);
}

$stmt->execute();

// if (!empty($_POST['doc_ids'])) {
//     $conn->prepare("DELETE FROM crm_document_links WHERE crm_module = 'lead' AND module_ref_id = ?")->execute([$lead_id]);

//     foreach ($_POST['doc_ids'] as $doc_id) {
//         $conn->prepare("INSERT INTO crm_document_links (document_id, crm_module, module_ref_id, linked_by)
//                        VALUES (?, 'lead', ?, ?)")->execute([$doc_id, $lead_id, $_SESSION['user_id']]);
//     }
// }

header("Location: ./");
exit;
