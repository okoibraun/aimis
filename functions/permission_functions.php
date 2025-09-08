<?php

function get_all_permissions() {
    global $conn;
    $stmt = $conn->query("SELECT id, name, description FROM permissions ORDER BY name ASC");
    return $stmt->fetch_all(MYSQLI_ASSOC);
}

function get_all_permissions_by_company($company_id) {
    global $conn;
    $stmt = $conn->query("SELECT id, name, description FROM permissions WHERE company_id = $company_id ORDER BY name ASC");
    return $stmt->fetch_all(MYSQLI_ASSOC);
}

function get_permissions_by_role($role_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['permission_id'];
    }
    return $ids;
}

function get_permissions_by_user($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT permission_id FROM user_permissions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['permission_id'];
    }
    return $ids;
}

function assign_permissions_to_role($company_id, $role_id, $permission_ids) {
    global $conn;
    // Clear old ones
    $stmt = $conn->prepare("DELETE FROM role_permissions WHERE company_id = ? AND role_id = ?");
    $stmt->bind_param("ii", $company_id, $role_id);
    $stmt->execute();

    // Insert new
    if (!empty($permission_ids)) {
        $stmt = $conn->prepare("INSERT INTO role_permissions (company_id, role_id, permission_id) VALUES (?, ?, ?)");
        foreach ($permission_ids as $perm_id) {
            $pid = intval($perm_id);
            $stmt->bind_param("isi", $company_id, $role_id, $pid);
            $stmt->execute();
        }
    }

    return true;
}

function assign_permissions_to_user($company_id, $user_id, $permission_ids) {
    global $conn;
    // Clear old ones
    $stmt = $conn->prepare("DELETE FROM user_permissions WHERE company_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $company_id, $user_id);
    $stmt->execute();

    // Insert new
    if (!empty($permission_ids)) {
        $stmt = $conn->prepare("INSERT INTO user_permissions (company_id, user_id, permission_id) VALUES (?, ?, ?)");
        foreach ($permission_ids as $perm_id) {
            $pid = intval($perm_id);
            $stmt->bind_param("iii", $company_id, $user_id, $pid);
            $stmt->execute();
        }
    }

    return true;
}

function delete_permission($permission_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM permissions WHERE id = ?");
    $stmt->bind_param("i", $permission_id);
    return $stmt->execute();
}

function get_permission_by_id($permission_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM permissions WHERE id = ?");
    $stmt->bind_param("i", $permission_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function update_permission($permission_id, $name, $description) {
    global $conn;
    $stmt = $conn->prepare("UPDATE permissions SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $description, $permission_id);
    return $stmt->execute();
}

// function get_permissions_by_user($user_id) {
//     global $conn;
//     $stmt = $conn->prepare("
//         SELECT p.id, p.name, p.description
//         FROM permissions p
//         JOIN role_permissions rp ON p.id = rp.permission_id
//         JOIN user_company uc ON uc.role_id = rp.role_id
//         WHERE uc.user_id = ?
//     ");
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// }

// function user_has_permission($user_id, $permission_name) {
//     global $conn;
//     $stmt = $conn->prepare("
//         SELECT p.id
//         FROM permissions p
//         JOIN role_permissions rp ON p.id = rp.permission_id
//         JOIN user_company uc ON uc.role_id = rp.role_id
//         WHERE uc.user_id = ? AND p.name = ?
//     ");
//     $stmt->bind_param("is", $user_id, $permission_name);
//     $stmt->execute();
//     return $stmt->get_result()->num_rows > 0;
// }

/**
 * Create a new permission.
 */
function create_permission($company_id, $name, $description = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO permissions (company_id, name, description) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $company_id, $name, $description);
    return $stmt->execute();
}

