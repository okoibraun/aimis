<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$id = intval($_GET['id']);
$company_id = get_current_company_id();
$user_id = get_current_user_id();

$stmt = $conn->prepare("UPDATE crm_reminders SET is_done = 1 WHERE id = ? AND company_id = ? AND user_id = ?");
$stmt->bind_param("iii", $id, $company_id, $user_id);
$stmt->execute();

if(isset($_GET['reflink']) && $_GET['reflink'] == "home") {
    header("Location: /");
    exit;
} else {
    header("Location: ./");
    exit;
}
