<?php
function get_all_plans() {
    global $conn;
    $result = $conn->query("SELECT * FROM plans ORDER BY price ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_company_subscription($company_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT s.*, p.name AS plan_name 
        FROM subscriptions s 
        JOIN plans p ON s.plan_id = p.id 
        WHERE s.company_id = ? 
        ORDER BY s.created_at DESC LIMIT 1
    ");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function get_subscription_history($company_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT s.*, p.name AS plan_name 
        FROM subscriptions s 
        JOIN plans p ON s.plan_id = p.id 
        WHERE s.company_id = ? 
        ORDER BY s.created_at DESC
    ");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
