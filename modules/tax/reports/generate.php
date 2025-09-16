<?php
session_start();
include_once '../../../config/db.php';
include_once '../functions/log_event.php';
include("../../../functions/role_functions.php");

$report_type = $_POST['report_type'];
$from = $_POST['from'];
$to = $_POST['to'];

$array_data = [];

if($report_type == "VAT") {
  $invoices = $conn->query("
  SELECT si.*, com.name AS company_name
  FROM sales_invoices si
  JOIN companies com ON com.id = si.company_id
  WHERE vat_tax_amount != '' AND payment_status = 'paid' AND si.company_id = $company_id AND si.invoice_date >= '$from' AND si.invoice_date <= '$to'");

  $total_tax_amount = 0;

  foreach($invoices as $invoice) {
    $total_tax_amount += $invoice['vat_tax_amount'];

    array_push($array_data, [
      'generated_for' => $report_type,
      'company_name' => $invoice['company_name'],
      'date_from' => $from,
      'date_to' => $to,
      'date_generated' => date('Y-m-d H:i:s'),
      'invoice_date' => $invoice['invoice_date'],
      'invoice_total_amount' => $invoice['total_amount'],
      'invoice_amount_paid' => $invoice['amount_paid'],
      'total_tax_amount' => $total_tax_amount,
      'invoice_number' => $invoice['invoice_number'],
      'order_number' => $invoice['order_number'],
      'quotation_number' => $invoice['quote_number'],
      'invoice_id' => $invoice['id'],
      'vat_tax_amount' => $invoice['vat_tax_amount']
    ]);
    
  }

  //   Convert to JSON
  $json_data = json_encode($array_data);

  $stmt = $conn->prepare("INSERT INTO tax_reports (company_id, report_type, date_from, date_to, report_data, generated_by) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issssi", $company_id, $report_type, $from, $to, $json_data, $user_id);
  
  if ($stmt->execute()) {
      // Log the event
      log_tax_event($conn, $company_id, $user_id, 'tax', 'REPORT_GENERATED', 'vat_tax_report', $conn->insert_id, "Type: {$_POST['report_type']}, Format: JSON");
      // Set success message
      $_SESSION['success'] = "Report generated successfully!";
      header("Location: ./");
      exit;
  } else {
      $_SESSION['error'] = "Failed to generate report: " . $stmt->error;
      header("Location: ./");
      exit;
  }
  
} else if($report_type == "WHT") {
  $invoices = $conn->query("
  SELECT si.*, com.name AS company_name
  FROM sales_invoices si
  JOIN companies com ON com.id = si.company_id
  WHERE wht_tax_amount != '' AND payment_status = 'paid' AND si.company_id = $company_id AND si.invoice_date >= '$from' AND si.invoice_date <= '$to'");

  $total_tax_amount = 0;

  foreach($invoices as $invoice) {
    $total_tax_amount += $invoice['wht_tax_amount'];

    array_push($array_data, [
      'generated_for' => $report_type,
      'company_name' => $invoice['company_name'],
      'date_from' => $from,
      'date_to' => $to,
      'date_generated' => date('Y-m-d H:i:s'),
      'invoice_date' => $invoice['invoice_date'],
      'invoice_total_amount' => $invoice['total_amount'],
      'invoice_amount_paid' => $invoice['amount_paid'],
      'total_tax_amount' => $total_tax_amount,
      'invoice_number' => $invoice['invoice_number'],
      'order_number' => $invoice['order_number'],
      'quotation_number' => $invoice['quote_number'],
      'invoice_id' => $invoice['id'],
      'wht_tax_amount' => $invoice['wht_tax_amount']
    ]);
    
  }
  
//   Convert to JSON
  $json_data = json_encode($array_data);

  $stmt = $conn->prepare("INSERT INTO tax_reports (company_id, report_type, date_from, date_to, report_data, generated_by) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issssi", $company_id, $report_type, $from, $to, $json_data, $user_id);
  
  if ($stmt->execute()) {
      // Log the event
      log_tax_event($conn, $company_id, $user_id, 'tax', 'REPORT_GENERATED', 'wht_tax_report', $conn->insert_id, "Type: {$_POST['report_type']}, Format: JSON");
      // Set success message
      $_SESSION['success'] = "Report generated successfully!";
      header("Location: ./");
      exit;
  } else {
      $_SESSION['error'] = "Failed to generate report: " . $stmt->error;
      header("Location: ./");
      exit;
  }
} else {
  $_SESSION['message'] = "Report Not Avaialble";
  header("Location: ./");
  exit;
}
