<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

$company_id = get_current_company_id();
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = trim($_POST['name']);
$industry = trim($_POST['industry']);
$phone = trim($_POST['phone']);
$website = trim($_POST['website']);
$notes = trim($_POST['notes']);

if ($id > 0) {
    // Update
    $stmt = $conn->prepare("UPDATE crm_companies SET name=?, industry=?, phone=?, website=?, notes=? WHERE id=? AND company_id=?");
    $stmt->bind_param("ssssssi", $name, $industry, $phone, $website, $notes, $id, $company_id);
} else {
    // Insert
    $stmt = $conn->prepare("INSERT INTO crm_companies (company_id, name, industry, phone, website, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $company_id, $name, $industry, $phone, $website, $notes);
}

$stmt->execute();
header("Location: ./");
exit;
