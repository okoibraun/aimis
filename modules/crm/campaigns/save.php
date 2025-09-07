<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();

$campaign_id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
$campaign_name   = trim($_POST['campaign_name']);
$segment_id      = intval($_POST['segment_id']);
$medium          = trim($_POST['medium']); // 'email', 'sms', etc.
$message_template = trim($_POST['message_template']);
$status          = trim($_POST['status']);
$scheduled_at    = !empty($_POST['scheduled_at']) ? date('Y-m-d H:i:s', strtotime($_POST['scheduled_at'])) : null;

if ($campaign_id > 0) {
    // UPDATE existing campaign
    $stmt = $conn->prepare("
        UPDATE crm_campaigns
        SET campaign_name = ?, segment_id = ?, medium = ?, message_template = ?, status = ?, scheduled_at = ?
        WHERE id = ? AND company_id = ?
    ");
    $stmt->bind_param(
        "sissssii",
        $campaign_name, $segment_id, $medium, $message_template, $status, $scheduled_at,
        $campaign_id, $company_id
    );
} else {
    // INSERT new campaign
    $stmt = $conn->prepare("
        INSERT INTO crm_campaigns
        (campaign_name, segment_id, medium, message_template, status, scheduled_at, company_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sissssi",
        $campaign_name, $segment_id, $medium, $message_template, $status, $scheduled_at, $company_id
    );
}

$stmt->execute();
$stmt->close();

// Redirect to list
header("Location: list.php");
exit;
