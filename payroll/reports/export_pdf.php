<?php
session_start();
include '../../config/db.php';
require('../../vendor/fpdf/fpdf.php');

$month = $_GET['month'] ?? date('Y-m');
$company_id = $_SESSION['company_id'];

$sql = "SELECT e.id, e.first_name, e.last_name, e.department, p.month, p.basic_salary, p.tax_deduction, p.nin_contribution, p.net_salary
        FROM payslips p
        JOIN employees e ON p.employee_id = e.id
        WHERE e.company_id = $company_id AND e.company_id = p.company_id AND p.month = '$month'";

$result = $conn->query($sql);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,"Payroll Summary Report - $month",0,1,'C');
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,10,"Employee");
$pdf->Cell(30,10,"Department");
$pdf->Cell(25,10,"Gross");
$pdf->Cell(20,10,"Tax");
$pdf->Cell(25,10,"NIN");
$pdf->Cell(25,10,"Net", 0, 1);

$pdf->SetFont('Arial','',10);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40,10, $row['first_name'] . ' ' . $row['last_name']);
    $pdf->Cell(30,10, $row['department']);
    $pdf->Cell(25,10, $row['basic_salary']);
    $pdf->Cell(20,10, $row['tax_deduction']);
    $pdf->Cell(25,10, $row['nin_contribution']);
    $pdf->Cell(25,10, $row['net_salary'], 0, 1);
}

$pdf->Output();
?>
