<?php
include '../../config/db.php';

$month = $_GET['month'] ?? date('Y-m');

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="bank_payments_' . $month . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Bank Name', 'Account Number', 'Account Name', 'Amount']);

$sql = "SELECT e.bank_name, e.account_number, e.account_name, p.net_salary
        FROM payroll p
        JOIN employees e ON p.employee_id = e.id
        WHERE p.month = '$month' AND p.paid_status = 0";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['bank_name'],
        $row['account_number'],
        $row['account_name'],
        $row['net_salary']
    ]);
}
fclose($output);
?>
