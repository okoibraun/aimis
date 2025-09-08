<?php
require_once '../../../config/db.php';
require_once '../includes/helpers.php';
include("../../../functions/role_functions.php");

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT INTO sales_leads (company_id, user_id, employee_id, customer_id, title, description, lead_date, source, status, assigned_to)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiisssssi',
        $_POST['company_id'],
        $user_id,
        $employee_id,
        $_POST['customer_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['lead_date'],
        $_POST['source'],
        $_POST['status'],
        $_POST['assigned_to']
    );
    $stmt->execute();
    $stmt->close();
    header("Location: ../views/leads/");

} elseif ($action === 'edit' && $id && $company_id === $_POST['company_id']) {
    $stmt = $conn->prepare("UPDATE sales_leads SET customer_id=?, title=?, description=?, lead_date=?, status=?, assigned_to=? WHERE id=? AND company_id=?");
    $stmt->bind_param('issssiii',
        $_POST['customer_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['lead_date'],
        $_POST['status'],
        $_POST['assigned_to'],
        $id,
        $company_id
    );
    $stmt->execute();
    $stmt->close();
    header("Location: ../views/leads/");

} elseif ($action === 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM sales_leads WHERE id=? AND company_id=?");
    $stmt->bind_param('ii', $id, $company_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ../views/leads/");

} else {
    echo "Invalid action.";
}
