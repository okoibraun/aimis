<?php
include '../../config/db.php';

if (!isset($_GET['pay_period'])) {
    die("No period provided.");
}

$period = $conn->real_escape_string($_GET['pay_period']);
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=bank_payment_' . $period . '.csv');

$output = fopen("php://output", "w");
fputcsv($output, ['Employee Name', 'Bank Name', 'Account Number', 'Amount']);

$sql = "
SELECT 
    e.first_name, e.last_name,
    e.bank_name, e.bank_account_number,
    p.net_salary 
FROM 
    payroll p
JOIN 
    employees e ON p.employee_id = e.id
WHERE 
    p.pay_period = '$period'
";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['first_name'] . ' ' . $row['last_name'],
        $row['bank_name'],
        $row['bank_account_number'],
        number_format($row['net_salary'], 2)
    ]);
}

fclose($output);
exit;
