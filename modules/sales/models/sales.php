<?php

function get_monthly_targets($user_id, $month) {
    global $db;
    return $db->fetch("SELECT * FROM sales_targets WHERE user_id = ? AND target_month = ?", [$user_id, $month]);
}

function get_monthly_sales($user_id, $month) {
    global $db;
    return $db->fetch("SELECT SUM(total_amount) as total FROM sales_invoices WHERE created_by = ? AND invoice_date >= ? AND invoice_date < DATE_ADD(?, INTERVAL 1 MONTH)", [$user_id, $month, $month]);
}
