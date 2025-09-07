<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Create a new role.
 */
// function create_role($name, $description = '') {
//     global $mysqli;

//     $stmt = $mysqli->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
//     $stmt->bind_param('ss', $name, $description);
//     return $stmt->execute();
// }

function create_role($company_id, $role, $name, $description) {
    global $conn;
    // Prevent duplicates
    $stmt = $conn->prepare("SELECT id FROM roles WHERE role = ? AND company_id = ?");
    $stmt->bind_param("si", $name, $company_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) return false;

    $stmt = $conn->prepare("INSERT INTO roles (company_id, role, name, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $company_id, $role, $name, $description);
    return $stmt->execute();
}

/**
 * Get roles by company ID.
 */
function get_roles_by_company($company_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name, role FROM roles WHERE company_id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Assign a role to a user in a company.
 */
// function assign_role_to_user($user_id, $company_id, $role_id) {
//     global $mysqli;

//     $stmt = $mysqli->prepare("
//         UPDATE user_company SET role_id = ? 
//         WHERE user_id = ? AND company_id = ?
//     ");
//     $stmt->bind_param('iii', $role_id, $user_id, $company_id);
//     return $stmt->execute();
// }

function assign_role_to_user($user_id, $role_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("ii", $role_id, $user_id);
    return $stmt->execute();
}


/**
 * Get all roles.
 */
function get_all_roles() {
    global $conn;

    return $conn->query("SELECT * FROM roles ORDER BY name");
}

/**
 * Create a new permission.
 */
// function create_permission($name, $description = '') {
//     global $mysqli;

//     $stmt = $mysqli->prepare("INSERT INTO permissions (name, description) VALUES (?, ?)");
//     $stmt->bind_param('ss', $name, $description);
//     return $stmt->execute();
// }

/**
 * Assign a permission to a role.
 */
function assign_permission_to_role($role_id, $permission_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $role_id, $permission_id);
    return $stmt->execute();
}

/**
 * Assign a permission to a role.
 */
function assign_permission_to_user($company_id, $user_id, $permission_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("INSERT IGNORE INTO user_permissions (company_id, user_id, permission_id) VALUES (?, ?, ?)");
    $stmt->bind_param('iii', $company_id, $user_id, $permission_id);
    return $stmt->execute();
}

/**
 * Get permissions of a role.
 */
// function get_permissions_by_role($role_id) {
//     global $mysqli;

//     $stmt = $mysqli->prepare("
//         SELECT p.* 
//         FROM permissions p
//         JOIN role_permission rp ON rp.permission_id = p.id
//         WHERE rp.role_id = ?
//     ");
//     $stmt->bind_param('i', $role_id);
//     $stmt->execute();
//     return $stmt->get_result();
// }

/**
 * Get permissions of a user in a company.
 */
// function get_user_permissions($user_id, $company_id) {
//     global $mysqli;

//     $stmt = $mysqli->prepare("
//         SELECT p.*
//         FROM permissions p
//         JOIN role_permission rp ON rp.permission_id = p.id
//         JOIN user_company uc ON uc.role_id = rp.role_id
//         WHERE uc.user_id = ? AND uc.company_id = ?
//     ");
//     $stmt->bind_param('ii', $user_id, $company_id);
//     $stmt->execute();
//     return $stmt->get_result();
// }


function get_user_permissions($user_id) {
  global $conn;
  
  $user_permissions = [];
  $get_user_permissions = $conn->query("SELECT * FROM user_permissions WHERE user_id = $user_id");

  foreach($get_user_permissions as $user_permission) {
    $permission_id = $user_permission['permission_id'];
    $permission = $conn->query("SELECT name FROM permissions WHERE id = $permission_id")->fetch_assoc();
    $user_permissions[] = strtolower($permission['name']);
  }

  return $user_permissions;
}

function get_available_roles_for_user($session) {
    global $conn;
    // Superadmins can assign any role
    //if ($session['role'] === 'superadmin') {
    if(in_array($session['role'], ['superadmin', 'system'])) {
        
        // return ['superadmin', 'admin', 'staff', 'viewer'];
        $roles = [];
        // $get_roles = ($session['role'] === "system") ? $conn->query("SELECT role FROM roles") : $conn->query("SELECT role FROM roles WHERE company_id = " . intval($session['company_id']));
        $get_roles = $conn->query("SELECT role FROM roles");
        // $get_roles = $conn->query("SELECT role FROM roles");
        foreach($get_roles as $role) {
            $roles[] = $role['role'];

            // it can also be implemented like below
            // array_push($roles, $role['role']);
        }
        return $roles;
    }
    // Admins can assign limited roles
    return ['staff', 'viewer'];
}

function super_roles() {
    $roles = ['superadmin', 'system'];
    return $roles;
}

function system_users() {
    $roles = ['system'];
    return $roles;
}

function departmental_roles() {
    $roles = ['hr', 'accounts', 'sales'];
    return $roles;
}

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'];
$employee_id = $_SESSION['employee_id'] ?? 0;