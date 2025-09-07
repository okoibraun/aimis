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
    $pay_stmt = $db->prepare("INSERT INTO sales_invoice_payments (invoice_id, amount, payment_date, payment_method, notes) VALUES (?, ?, ?, ?, ?)");
    $pay = $pay_stmt->execute([
        $invoice_id,
        $amount,
        $paid_at,
        $_POST['payment_method'],
        $_POST['notes']
    ]);

    // Update invoice
    $invoice = $db->query("SELECT total_amount, paid_amount FROM sales_invoices WHERE id = $invoice_id")->fetch_assoc();
    $new_paid = $invoice['paid_amount'] + $amount;
    $status = $new_paid >= $invoice['total_amount'] ? 'paid' : 'partial';
    
    $db->query("UPDATE sales_invoices SET paid_amount = $new_paid, payment_status = '$status' WHERE id = $invoice_id");

    redirect('../views/payments/');
}

