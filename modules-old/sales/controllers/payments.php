<?php
require_once '../includes/helpers.php'; // Include your helper functions

$action = $_GET['action'] ?? 'report';
$id = $_GET['id'] ?? null;


if ($action === 'report') {
    redirect('../views/payments/reports.php');
    exit;
}

if ($action === 'add') {
    redirect('../views/payments/add.php?id=' . $id);
    exit;
}

if ($action === 'save') {
    $invoice_id = $_POST['invoice_id'];
    $amount = $_POST['amount_paid'];
    $paid_at = $_POST['paid_at'] ?? date('Y-m-d H:i:s');
    // $amount = floatval(str_replace(',', '', $amount)); // Sanitize amount input
    $db->query("INSERT INTO sales_invoice_payments (invoice_id, amount_paid, paid_at, payment_method, notes) VALUES (?, ?, ?, ?, ?)", [
        $invoice_id,
        $amount,
        $paid_at,
        $_POST['payment_method'],
        $_POST['notes']
    ]);

    // Update invoice
    $invoice = $db->fetch("SELECT total_amount, paid_amount FROM sales_invoices WHERE id = ?", [$invoice_id]);
    $new_paid = $invoice['paid_amount'] + $amount;
    $status = $new_paid >= $invoice['total_amount'] ? 'paid' : 'partial';
    
    $db->query("UPDATE sales_invoices SET paid_amount = ?, payment_status = ? WHERE id = ?", [
        $new_paid,
        $status,
        $invoice_id
    ]);

    redirect('payments.php?action=report');
}

