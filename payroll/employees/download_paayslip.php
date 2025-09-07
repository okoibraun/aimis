<?php
require('../../vendor/fpdf/fpdf.php');
include '../../config/db.php';

$id = $_GET['id'];
$row = $conn->query("SELECT p.*, e.first_name, e.last_name FROM payroll p JOIN employees e ON p.employee_id = e.id WHERE p.id = $id")->fetch_assoc();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Payslip for ' . $row['month'],0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Employee: ' . $row['first_name'] . ' ' . $row['last_name'],0,1);
$pdf->Cell(0,10,'Gross: ' . $row['gross_salary'],0,1);
$pdf->Cell(0,10,'Tax: ' . $row['tax'],0,1);
$pdf->Cell(0,10,'Social Security: ' . $row['social_security'],0,1);
$pdf->Cell(0,10,'Net: ' . $row['net_salary'],0,1);
$pdf->Output();
?>
