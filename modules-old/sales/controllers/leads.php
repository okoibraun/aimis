<?php
require_once '../../../config/db.php';
require_once '../includes/helpers.php';

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT INTO sales_leads (customer_id, title, description, lead_date, source, status, assigned_to)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssssi',
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
    header("Location: ../views/leads/list.php");

} elseif ($action === 'edit' && $id) {
    $stmt = $conn->prepare("UPDATE sales_leads SET customer_id=?, title=?, description=?, lead_date=?, status=?, assigned_to=? WHERE id=?");
    $stmt->bind_param('issssii',
        $_POST['customer_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['lead_date'],
        $_POST['status'],
        $_POST['assigned_to'],
        $id
    );
    $stmt->execute();
    $stmt->close();
    header("Location: ../views/leads/list.php");

} elseif ($action === 'delete' && $id) {
    $stmt = $conn->prepare("DELETE FROM sales_leads WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header("Location: ../views/leads/list.php");

} else {
    echo "Invalid action.";
}
