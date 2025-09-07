<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Assign a plan to a company (start or update subscription).
 */
function assign_plan_to_company($company_id, $plan_id, $start_date = null) {
    global $mysqli;

    $start_date = $start_date ?? date('Y-m-d');
    $stmt = $mysqli->prepare("
        INSERT INTO subscriptions (company_id, plan_id, start_date)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
        plan_id = VALUES(plan_id), start_date = VALUES(start_date)
    ");
    $stmt->bind_param('iis', $company_id, $plan_id, $start_date);
    return $stmt->execute();
}

/**
 * Get subscription info for a company.
 */
function get_subscription_by_company($company_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        SELECT s.*, p.name AS plan_name, p.description, p.price
        FROM subscriptions s
        JOIN plans p ON s.plan_id = p.id
        WHERE s.company_id = ?
    ");
    $stmt->bind_param('i', $company_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get all plans.
 */
function get_all_plans() {
    global $mysqli;

    $result = $mysqli->query("SELECT * FROM plans ORDER BY price ASC");
    return $result;
}

/**
 * Log subscription changes (history tracking).
 */
function log_subscription_history($company_id, $plan_id, $action) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        INSERT INTO subscription_history (company_id, plan_id, action)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param('iis', $company_id, $plan_id, $action);
    return $stmt->execute();
}

/**
 * Get billing/subscription history.
 */
function get_subscription_history($company_id) {
    global $mysqli;

    $stmt = $mysqli->prepare("
        SELECT h.*, p.name AS plan_name
        FROM subscription_history h
        JOIN plans p ON h.plan_id = p.id
        WHERE h.company_id = ?
        ORDER BY h.date DESC
    ");
    $stmt->bind_param('i', $company_id);
    $stmt->execute();
    return $stmt->get_result();
}
