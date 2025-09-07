<?php
require_once __DIR__ . '/../config/db.php';

function create_company($name, $industry, $group_id = null) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO companies (name, industry, group_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $industry, $group_id);
    return $stmt->execute() ? $conn->insert_id : false;
}

function get_user_companies($user_id) {
    global $conn;
    $sql = "SELECT c.* FROM companies c
            JOIN user_company_roles ucr ON c.id = ucr.company_id
            WHERE ucr.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function get_company($company_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function update_company($id, $name, $industry) {
    global $conn;
    $stmt = $conn->prepare("UPDATE companies SET name = ?, industry = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $industry, $id);
    return $stmt->execute();
}

function delete_company($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM companies WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function get_company_users($company_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT u.id, u.name, u.email, r.name AS role 
                            FROM users u
                            JOIN user_company_roles ucr ON u.id = ucr.user_id
                            JOIN roles r ON r.id = ucr.role_id
                            WHERE ucr.company_id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    return $stmt->get_result();
}

function get_company_groups($company_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT g.* FROM groups g
                            JOIN companies c ON g.company_id = c.id
                            WHERE c.id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    return $stmt->get_result();
}

function get_company_roles($company_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT r.* FROM roles r
                            JOIN user_company_roles ucr ON r.id = ucr.role_id
                            WHERE ucr.company_id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    return $stmt->get_result();
}

function get_company_industries() {
    global $conn;
    $stmt = $conn->prepare("SELECT DISTINCT industry FROM companies");
    $stmt->execute();
    return $stmt->get_result();
}

function get_company_groups_by_user($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT g.* FROM groups g
                            JOIN user_company_roles ucr ON g.company_id = ucr.company_id
                            WHERE ucr.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function get_company_roles_by_user($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT r.* FROM roles r
                            JOIN user_company_roles ucr ON r.id = ucr.role_id
                            WHERE ucr.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

