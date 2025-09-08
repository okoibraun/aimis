<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$company_id = $_SESSION['company_id'];
$employee_id = $_GET['id'];
$hold_payment = $conn->query("UPDATE employees SET status='hold' WHERE id = $employee_id AND company_id = $company_id");

if($hold_payment) {
    $_SESSION['success'] = "Employee put on Hold successfully";
    header("Location: view_employee.php?id={$employee_id}");
} else {
    header("Location: list_employees.php");
}
?>
