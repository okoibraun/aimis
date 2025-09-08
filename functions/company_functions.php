<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Create a new company.
 */
// function create_company($name, $group_id = null) {
//     global $mysqli;

//     $stmt = $mysqli->prepare("
//         INSERT INTO companies (name, group_id) VALUES (?, ?)
//     ");
//     $stmt->bind_param('si', $name, $group_id);
//     return $stmt->execute();
// }

function create_company($user_id, $name, $industry, $is_parent, $description, $parent_company_id = null)
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO companies (user_id, name, industry, is_parent, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issis", $user_id, $name, $industry, $is_parent, $description);
    return $stmt->execute() ? $stmt->insert_id : false;
}


/**
 * Update company details.
 */
function update_company($company_id, $name, $group_id = null) {
    global $mysqli;

    $stmt = $mysqli->prepare("UPDATE companies SET name = ?, group_id = ? WHERE id = ?");
    $stmt->bind_param('sii', $name, $group_id, $company_id);
    return $stmt->execute();
}

/**
 * Update company profile.
 */
function update_company_profile($company_id, $name, $industry, $address, $email, $phone) {
    global $conn;
    $stmt = $conn->prepare("UPDATE companies SET name=?, industry=?, address=?, email=?, phone=? WHERE id=?");
    $stmt->execute([$name, $industry, $address, $email, $phone, $company_id]);
}

/**
 * Update company name.
 */
function update_company_name($id, $name) {
    global $conn;
    $stmt = $conn->prepare("UPDATE companies SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);
    return $stmt->execute();
}

// Checks if a user has the right to manage a given company
function user_can_manage_company($session, $company) {
    if (in_array($session['role'], ['superadmin', 'system', 'admin'])) {
        return true;
    }
    return isset($company['parent_company_id']) && $company['parent_company_id'] == $session['company_id'];
}

/**
 * Delete a company (permanently).
 * Consider soft-delete logic for production use.
 */
function delete_company($company_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("DELETE FROM companies WHERE id = ?");
    $stmt->bind_param('i', $company_id);
    return $stmt->execute();
}

/**
 * Get all companies for a user.
 */
function get_companies_by_user($user_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        SELECT c.* 
        FROM companies c
        JOIN user_company uc ON c.id = uc.company_id
        WHERE uc.user_id = ?
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Get company by ID.
 */
function get_company_by_id($company_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->bind_param('i', $company_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * List all company groups.
 */
// function get_all_company_groups() {
//     global $mysqli;

//     $result = $mysqli->query("SELECT * FROM company_groups ORDER BY name");
//     return $result;
// }

function create_company_group($group_name, $company_id) {
    global $conn;

    $conn->begin_transaction();

    try {
        $stmt = mysqli_query($conn, "INSERT INTO company_groups (company_id, name) VALUES ('$company_id', '$group_name')");
        if($stmt) {
            return true;
        }
        // $stmt = $conn->prepare("INSERT INTO company_groups (company_id, name) VALUES (??)");
        // $stmt->bind_param("is", $company_id, $group_name);
        // $stmt->execute();
        // $group_id = $conn->insert_id;

        // $stmt = $conn->prepare("INSERT INTO company_group_members (group_id, company_id) VALUES (?, ?)");
        // foreach ($company_ids as $cid) {
        //     $cid = intval($cid);
        //     $stmt->bind_param("ii", $group_id, $cid);
        //     $stmt->execute();
        // }

        // $conn->commit();
        // return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

// function get_all_company_groups() {
//     global $conn;

//     $query = "
//         SELECT g.id, g.group_name, GROUP_CONCAT(c.name SEPARATOR ', ') AS company_names
//         FROM company_groups g
//         JOIN company_group_members gm ON g.id = gm.group_id
//         JOIN companies c ON gm.company_id = c.id
//         GROUP BY g.id, g.group_name
//         ORDER BY g.created_at DESC
//     ";
//     $res = $conn->query($query);
//     return $res->fetch_all(MYSQLI_ASSOC);
// }

function get_all_company_groups() {
    global $conn;

    $query = "
        SELECT g.id, g.company_id, g.name, GROUP_CONCAT(c.name SEPARATOR ', ') AS company_names
        FROM company_groups g
        JOIN companies c ON g.company_id = c.id
        GROUP BY g.id, g.name
        ORDER BY g.created_at DESC
    ";
    $res = $conn->query($query);
    return $res;
}


// Get all companies (for superadmin)
// function get_all_companies() {
//     global $conn;
//     $query = "SELECT c.*, p.name AS parent_name
//               FROM companies c
//               LEFT JOIN companies p ON c.parent_company_id = p.id
//               ORDER BY c.created_at DESC";
//     $result = $conn->query($query);
//     return $result->fetch_all(MYSQLI_ASSOC);
// }
function get_all_companies() {
    global $conn;
    $query = "SELECT * FROM companies ORDER BY created_at DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get companies by parent company group
function get_companies_by_group($parent_id) {
    global $conn;
    // $stmt = $conn->prepare("SELECT * FROM companies WHERE parent_company_id = ? ORDER BY created_at DESC");
    $stmt = $conn->prepare("SELECT * FROM companies WHERE id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_all(MYSQLI_ASSOC);
}

/**
 * Cross Company Access
 */
function grant_cross_company_access($user_id, $target_company_id, $granted_by) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO cross_company_access (user_id, target_company_id, granted_by) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $target_company_id, $granted_by);
    return $stmt->execute();
}

function get_cross_company_access($user_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT cca.*, c.name AS target_company 
        FROM cross_company_access cca 
        JOIN companies c ON cca.target_company_id = c.id 
        WHERE cca.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function revoke_cross_company_access($access_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM cross_company_access WHERE id = ?");
    $stmt->bind_param("i", $access_id);
    return $stmt->execute();
}

