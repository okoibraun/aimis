<?php
function log_activity($user_id, $company_id, $action, $description = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, company_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $user_id, $company_id, $action, $description, $ip_address, $user_agent);
    $stmt->execute();
}

function get_activity_logs_by_company($company_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT al.*, u.email 
        FROM activity_logs al 
        JOIN users u ON al.user_id = u.id 
        WHERE al.company_id = ?
        ORDER BY al.created_at DESC
    ");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_activity_logs_by_user($user_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT al.*, c.name AS company_name 
        FROM activity_logs al 
        JOIN companies c ON al.company_id = c.id 
        WHERE al.user_id = ?
        ORDER BY al.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
