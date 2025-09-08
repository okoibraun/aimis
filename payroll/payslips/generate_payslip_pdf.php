<?php
require_once '../../vendor/tecnickcom/tcpdf/tcpdf.php'; // Adjust path as needed
require_once '../../config/db.php';

if (!isset($_GET['id'])) {
    die('No payroll ID provided.');
}

$payroll_id = intval($_GET['id']);

// Get payroll record
$sql = "SELECT p.*, e.first_name, e.last_name, e.position, e.department, e.email 
        FROM payroll p 
        JOIN employees e ON p.employee_id = e.id 
        WHERE p.id = $payroll_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Payslip not found.");
}

$row = $result->fetch_assoc();
$full_name = $row['first_name'] . ' ' . $row['last_name'];

// Generate PDF
$pdf = new TCPDF();
$pdf->SetCreator('Payroll System');
$pdf->SetAuthor('SRMS');
$pdf->SetTitle("Payslip - $full_name");
$pdf->AddPage();

$html = '
<h2 style="text-align:center;">Payslip</h2>
<table cellpadding="5" cellspacing="0" border="1" width="100%">
  <tr>
    <td><b>Employee Name:</b> ' . htmlspecialchars($full_name) . '</td>
    <td><b>Email:</b> ' . $row['email'] . '</td>
  </tr>
  <tr>
    <td><b>Position:</b> ' . $row['position'] . '</td>
    <td><b>Department:</b> ' . $row['department'] . '</td>
  </tr>
  <tr>
    <td><b>Month:</b> ' . $row['month'] . '</td>
    <td><b>Payment Date:</b> ' . ($row['payment_date'] ?: '-') . '</td>
  </tr>
</table><br>

<h4>Earnings</h4>
<table cellpadding="5" cellspacing="0" border="1" width="100%">
  <tr>
    <td><b>Basic Salary</b></td>
    <td>$' . number_format($row['basic_salary'], 2) . '</td>
  </tr>
  <tr>
    <td><b>Overtime</b></td>
    <td>$' . number_format($row['overtime'], 2) . '</td>
  </tr>
  <tr>
    <td><b>Bonus</b></td>
    <td>$' . number_format($row['bonus'], 2) . '</td>
  </tr>
  <tr>
    <td><b>Gross Salary</b></td>
    <td><b>$' . number_format($row['gross_salary'], 2) . '</b></td>
  </tr>
</table><br>

<h4>Deductions</h4>
<table cellpadding="5" cellspacing="0" border="1" width="100%">
  <tr>
    <td><b>Tax</b></td>
    <td>$' . number_format($row['tax'], 2) . '</td>
  </tr>
  <tr>
    <td><b>Social Security</b></td>
    <td>$' . number_format($row['social_security'], 2) . '</td>
  </tr>
  <tr>
    <td><b>Other Deductions</b></td>
    <td>$' . number_format($row['deductions'], 2) . '</td>
  </tr>
</table><br>

<h3 style="text-align:right;">Net Salary: $' . number_format($row['net_salary'], 2) . '</h3>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Payslip_{$row['month']}_{$full_name}.pdf", 'I');
