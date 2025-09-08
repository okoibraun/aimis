<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/helpers.php';


/**
 * Attempt login with email and password.
 */
function login_user($email, $password) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
    }

    return false;
}

/**
 * Check if user is logged in.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get logged-in user data.
 */
function get_logged_in_user() {
    global $mysqli;

    if (!is_logged_in()) return null;

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Log out current user.
 */
function logout_user() {
    session_unset();
    session_destroy();
}

/**
 * Check if current user has a specific role in a company.
 */
function user_has_role($company_id, $role_name) {
    global $mysqli;

    if (!is_logged_in()) return false;

    $stmt = $mysqli->prepare("
        SELECT r.name 
        FROM user_company uc
        JOIN roles r ON uc.role_id = r.id
        WHERE uc.user_id = ? AND uc.company_id = ?
    ");
    $stmt->bind_param('ii', $_SESSION['user_id'], $company_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return isset($result['name']) && $result['name'] === $role_name;
}

/**
 * Check is a user has permission
 */
function has_permission($user_id, $permission_key) {
    global $conn;
    $sql = "SELECT COUNT(*) FROM user_permissions up
            JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ? AND p.permission_key = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $permission_key);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count > 0;
}
