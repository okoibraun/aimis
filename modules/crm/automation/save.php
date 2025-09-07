<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();

$id = intval($_POST['id']);
$rule_name = $_POST['rule_name'];
$trigger_type = $_POST['trigger_type'];
$trigger_value = $_POST['trigger_value'];
$action_type = $_POST['action_type'];
$action_value = $_POST['action_value'];
$is_active = intval($_POST['is_active']);

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE crm_automation_rules SET rule_name=?, trigger_type=?, trigger_value=?, action_type=?, action_value=?, is_active=? WHERE id=? AND company_id=?");
    $stmt->bind_param("ssssssii", $rule_name, $trigger_type, $trigger_value, $action_type, $action_value, $is_active, $id, $company_id);
} else {
    $stmt = $conn->prepare("INSERT INTO crm_automation_rules (company_id, rule_name, trigger_type, trigger_value, action_type, action_value, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssi", $company_id, $rule_name, $trigger_type, $trigger_value, $action_type, $action_value, $is_active);
}

$stmt->execute();
$stmt->close();

header("Location: list.php");
exit;
