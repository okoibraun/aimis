<?php

function assign_role_to_user($user_id, $company_id, $role_id) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO user_company_roles (user_id, company_id, role_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $company_id, $role_id);
    return $stmt->execute();
}