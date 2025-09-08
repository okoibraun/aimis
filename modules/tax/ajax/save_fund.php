<?php
include_once '../../../config/db.php';

$id = $_POST['id'] ?? null;
$code = trim($_POST['fund_code']);
$name = trim($_POST['fund_name']);
$balance = floatval($_POST['balance']);
$company_id = $_SESSION['company_id'];

if ($id) {
    // Update
    $stmt = $conn->query("UPDATE tax_funds SET fund_code=$code, fund_name=$name, balance=$balance WHERE id=$id AND company_id=$company_id");
    if($stmt) {
        $_SESSION['success'] = "Fund updated successfully.";
        header('Location: ../funds.php');
        exit;
    } else {
        $_SESSION['error'] = "Failed to update fund.";
    }
} else {
    // Insert
    $stmt = $conn->query("INSERT INTO tax_funds (company_id, fund_code, fund_name, balance) VALUES ($company_id, $code, $name, $balance)");
    if($stmt) {
        $_SESSION['success'] = "Fund added successfully.";
        header('Location: ../funds.php');
        exit;
    } else {
        $_SESSION['error'] = "Failed to add fund.";
    }
}
