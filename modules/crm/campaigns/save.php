<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';
require_once '../../../functions/role_functions.php';


$campaign_id     = isset($_POST['id']) ? intval($_POST['id']) : null;
$campaign_name   = trim($_POST['campaign_name']);
$description = $_POST['description'];
$target_segment_id      = intval($_POST['target_segment_id']);
$target_type          = trim($_POST['target_type']); // lead or customer
$scheduled_at    = !empty($_POST['scheduled_at']) ? date('Y-m-d H:i:s', strtotime($_POST['scheduled_at'])) : null;
$status = $_POST['status'];

if ($campaign_id > 0) {
    // UPDATE existing campaign
    $stmt = $conn->prepare("
        UPDATE crm_campaigns
        SET campaign_name = ?, description = ?, target_segment_id = ?, target_type = ?, scheduled_at = ?, status = ?
        WHERE id = ? AND company_id = ?
    ");
    $stmt->bind_param(
        "ssisssii",
        $campaign_name, $description, $target_segment_id, $target_type, $scheduled_at, $status, $campaign_id, $company_id
    );
} else {
    // INSERT new campaign
    $stmt = $conn->prepare("
        INSERT INTO crm_campaigns
        (company_id, campaign_name, description, target_segment_id, target_type, scheduled_at, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ississi",
        $company_id, $campaign_name, $description, $target_segment_id, $target_type, $scheduled_at, $user_id
    );
}

$stmt->execute();
$stmt->close();

// Redirect to list
header("Location: ./");
exit;
