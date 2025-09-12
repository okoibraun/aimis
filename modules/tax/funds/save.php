<?php
session_start();
include_once '../../../config/db.php';
include("../../../functions/role_functions.php");
include("../functions/log_event.php");

$id = $_POST['id'] ?? null;
$code = trim($_POST['fund_code']);
$name = trim($_POST['fund_name']);
$balance = floatval($_POST['balance']);
$company_id = $_SESSION['company_id'];

if ($id) {
    // Update
    $stmt = $conn->query("UPDATE tax_funds SET fund_code='$code', fund_name='$name', balance=$balance WHERE id=$id AND company_id=$company_id");
    if($stmt) {
        // Log the event
        log_tax_event($conn, $company_id, $user_id, 'public_tax', 'UPDATED_FUNDS', 'tax_funds', $id, "Fund: {$_POST['fund_name']}, Code: {$_POST['fund_code']}, Balance: {$_POST['balance']}");
        // Update successful
        $_SESSION['success'] = "Fund updated successfully.";
        header('Location: ./');
        exit;
    } else {
        $_SESSION['error'] = "Failed to update fund.";
    }
} else {
    // Insert
    $stmt = $conn->query("INSERT INTO tax_funds (company_id, user_id, employee_id, fund_code, fund_name, balance) VALUES ($company_id, $user_id, $employee_id, '$code', '$name', $balance)");
    if($stmt) {
        // Log the event
        log_tax_event($conn, $company_id, $user_id, 'public_tax', 'CREATED_FUNDS', 'tax_funds', $conn->insert_id, "Fund: {$_POST['fund_name']}, Code: {$_POST['fund_code']}, Balance: {$_POST['balance']}");
        // Insert successful
        $_SESSION['success'] = "Fund added successfully.";
        header('Location: ./');
        exit;
    } else {
        $_SESSION['error'] = "Failed to add fund.";
    }
}
