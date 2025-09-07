<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Log an activity event (audit or general).
 */
function log_activity($user_id, $company_id, $action, $description = null, $type = 'activity') {
    global $mysqli;

    $stmt = $mysqli->prepare("
        INSERT INTO logs (user_id, company_id, action, description, type)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iisss', $user_id, $company_id, $action, $description, $type);
    return $stmt->execute();
}

/**
 * Fetch logs by company.
 */
function get_logs_by_company($company_id, $type = null) {
    global $mysqli;

    if ($type) {
        $stmt = $mysqli->prepare("
            SELECT l.*, u.email 
            FROM logs l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE l.company_id = ? AND l.type = ?
            ORDER BY l.timestamp DESC
        ");
        $stmt->bind_param('is', $company_id, $type);
    } else {
        $stmt = $mysqli->prepare("
            SELECT l.*, u.email 
            FROM logs l
            LEFT JOIN users u ON l.user_id = u.id
            WHERE l.company_id = ?
            ORDER BY l.timestamp DESC
        ");
        $stmt->bind_param('i', $company_id);
    }

    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Fetch logs by user.
 */
function get_logs_by_user($user_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        SELECT * FROM logs
        WHERE user_id = ?
        ORDER BY timestamp DESC
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
