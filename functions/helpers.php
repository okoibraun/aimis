<?php
session_start();
include("../config.db.php");
/**
 * Sanitize input string
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a specific path
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Generate a random token
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Format date
 */
function format_date($datetime, $format = 'Y-m-d H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Check if current user has a specific permission (assumes session is active)
 */
function user_has_permission($permission_name, $user_id, $company_id) {
    require_once __DIR__ . '/role_functions.php';
    $permissions = get_user_permissions($user_id, $company_id);
    while ($perm = $permissions->fetch_assoc()) {
        if ($perm['name'] === $permission_name) {
            return true;
        }
    }
    return false;
}

/**
 * Get current logged-in user ID
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current company ID (for company context)
 */
function get_current_company_id() {
    return $_SESSION['company_id'] ?? null;
}

/**
 * Debug helper (remove from production)
 */
function dd($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}

/**
 * Create a notification for a user
 * @param int $user_id
 * @param string $title
 * @param string $message
 */
function create_notification($user_id, $title, $message) {
    global $conn;
    $sql = "INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $title, $message);
    $stmt->execute();
}

/**
 * Get unread notifications for a user
 * @param int $user_id
 */
// function get_unread_notifications($user_id) {
//     global $conn;
//     $sql = "SELECT id, title FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_all(MYSQLI_ASSOC);
// }

function get_unread_notifications($user_id, $is_admin = false) {
    global $conn;
    if ($is_admin) {
        $sql = "SELECT id, title FROM notifications 
                WHERE (user_id = ? OR type = 'admin') AND is_read = 0 
                ORDER BY created_at DESC";
    } else {
        $sql = "SELECT id, title FROM notifications 
                WHERE user_id = ? AND is_read = 0 
                ORDER BY created_at DESC";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

