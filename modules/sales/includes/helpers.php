<?php
session_start();
// Include database connection and header
require_once __DIR__ . '/../../../config/db.php';

function get_all_rows($table) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM $table");
    $stmt->execute();
    return $stmt->get_result();
}

function get_row_by_id($table, $id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function delete_row_by_id($table, $id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function status_badge($status) {
  switch ($status) {
    case 'pending': return 'secondary';
    case 'confirmed': return 'info';
    case 'shipped': return 'success';
    case 'cancelled': return 'danger';
    default: return 'light';
  }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function get_invoice_items($invoice_id) {
    global $db;
    $stmt = $db->prepare("SELECT sii.*, p.name AS product_name
                          FROM sales_invoice_items sii
                          LEFT JOIN products p ON sii.product_id = p.id
                          WHERE sii.invoice_id = ?");
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
