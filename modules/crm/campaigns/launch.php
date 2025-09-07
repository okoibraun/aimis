<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$campaign_id = intval($_GET['id']);
$company_id = get_current_company_id();

// Load campaign
$camp_stmt = $conn->prepare("SELECT * FROM crm_campaigns WHERE id=? AND company_id=?");
$camp_stmt->bind_param("ii", $campaign_id, $company_id);
$camp_stmt->execute();
$campaign = $camp_stmt->get_result()->fetch_assoc();
$filters = json_decode($conn->query("SELECT filters FROM crm_segments WHERE id={$campaign['target_segment_id']}")->fetch_assoc()['filters'], true);

// Fetch targets from segment
$target_table = $campaign['target_type'] === 'contact' ? 'crm_contacts' : 'crm_companies';
$query = "SELECT id FROM $target_table WHERE company_id=?";
$params = [$company_id];
$types = 'i';

if (!empty($filters['tag'])) {
    $query .= " AND tags LIKE ?";
    $params[] = '%' . $filters['tag'] . '%';
    $types .= 's';
}
if (!empty($filters['status'])) {
    $query .= " AND status = ?";
    $params[] = $filters['status'];
    $types .= 's';
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$targets = $stmt->get_result();

// Insert into logs
$insert = $conn->prepare("INSERT INTO crm_campaign_logs (company_id, campaign_id, target_id, medium, status) VALUES (?, ?, ?, 'email', 'queued')");
foreach ($targets as $row) {
    $insert->bind_param("iii", $company_id, $campaign_id, $row['id']);
    $insert->execute();
}

// Mark campaign as active
$conn->query("UPDATE crm_campaigns SET status='active' WHERE id=$campaign_id");

header("Location: view.php?id=$campaign_id");
exit;
