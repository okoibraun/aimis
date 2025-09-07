<?php
session_start();
include '../../config/db.php';

$month = $_GET['month'] ?? date('Y-m');
$company_id = $_SESSION['company_id'];

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=payroll_summary_$month.xls");

echo "Employee\tDepartment\tGross\tTax\tSSN\tNet\n";

$sql = "SELECT e.id, e.first_name, e.last_name, e.department, p.month, p.basic_salary, p.tax_deduction, p.nin_contribution, p.net_salary
        FROM payslips p
        JOIN employees e ON p.employee_id = e.id
        WHERE e.company_id = $company_id AND e.company_id = p.company_id AND p.month = '$month'";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "{$row['first_name']} {$row['last_name']}\t{$row['department']}\t{$row['basic_salary']}\t{$row['tax_deduction']}\t{$row['nin_contribution']}\t{$row['net_salary']}\n";
}
?>
