<?php
require_once __DIR__ . '/../config/db.php';

// Get user role in a company
function get_user_role($user_id, $company_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT role_id FROM user_company_roles WHERE user_id = ? AND company_id = ?");
    $stmt->bind_param("ii", $user_id, $company_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['role_id'] : null;
}

// Check if role has permission
function role_has_permission($role_id, $permission) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 1 FROM role_permissions rp
        JOIN permissions p ON rp.permission_id = p.id
        WHERE rp.role_id = ? AND p.name = ?
        LIMIT 1
    ");
    $stmt->bind_param("is", $role_id, $permission);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

// Final check: can user perform action in a company
function user_can($user_id, $company_id, $permission) {
    $role_id = get_user_role($user_id, $company_id);
    if (!$role_id) return false;
    return role_has_permission($role_id, $permission);
}
