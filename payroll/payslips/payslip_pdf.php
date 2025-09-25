<?php
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');
include '../../config/db.php';

if (!isset($_GET['id'])) {
    die("Invalid payslip ID.");
}

$payslip_id = intval($_GET['id']);
$query = "SELECT p.*, e.first_name, e.last_name, e.employee_code, e.id
          FROM payslips p
          JOIN employees e ON p.employee_id = e.id
          WHERE p.id = $payslip_id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("Payslip not found.");
}

$row = $result->fetch_assoc();

// Create new PDF document
$pdf = new TCPDF();
$pdf->SetTitle('Payslip - ' . $row['first_name']);
$pdf->AddPage();

// Payslip Header
$html = '
<h2>Payslip for ' . date('F Y', strtotime($row['month'] . '-01')) . '</h2>
<table cellpadding="4">
    <tr>
        <td><strong>Employee Number/Code:</strong> ' . $row['employee_code'] . '</td>
        <td><strong>Name:</strong> ' . $row['first_name'] . ' ' . $row['last_name'] . '</td>
    </tr>
    <tr>
        <td><strong>Generated on:</strong> ' . date('Y-m-d H:i') . '</td>
        <td></td>
    </tr>
</table>

<h4>Salary Breakdown</h4>
<table border="1" cellpadding="6">
    <tr>
        <th align="left">Component</th>
        <th align="right">Amount (₦)</th>
    </tr>
    <tr>
        <td>Basic Salary</td>
        <td align="right">' . number_format($row['basic_salary'], 2) . '</td>
    </tr>
    <tr>
        <td>Allowances</td>
        <td align="right">' . number_format($row['allowances'], 2) . '</td>
    </tr>
    <tr>
        <td>Bonuses</td>
        <td align="right">' . number_format($row['bonuses'], 2) . '</td>
    </tr>
    <tr>
        <td>Deductions</td>
        <td align="right">-' . number_format($row['deductions'], 2) . '</td>
    </tr>
    <tr>
        <th align="left">Net Salary</th>
        <th align="right">' . number_format($row['net_salary'], 2) . '</th>
    </tr>
    <tr>
        <td>Tax Deduction (personal income tax)</td>
        <td align="right">-' . number_format($row['tax_deduction'], 2) . '</td>
    </tr>

</table>

<p style="margin-top:30px;">This is a computer-generated payslip and does not require a signature.</p>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('payslip_' . $row['employee_id'] . '_' . $row['month'] . '.pdf', 'I');
