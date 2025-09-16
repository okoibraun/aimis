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
  $orders = $conn->query("SELECT * FROM sales_orders WHERE company_id = $company_id AND order_date >= '$from' AND order_date <= '$to'");
  $total_order_tax = 0;

  foreach($orders as $oi => $order) {
    $total_order_tax += $order['tax_amount'];

    $items = $conn->query("
    SELECT i.*, p.name AS product_name, o.*
    FROM sales_order_items i
    JOIN sales_products p ON p.id = i.product_id
    JOIN sales_orders o ON o.id = i.order_id
    WHERE i.order_id = {$order['id']}");

    array_push($array_data, [
      'generated_for' => $report_type,
      'company_name' => $conn->query("SELECT name FROM companies WHERE id = $company_id")->fetch_assoc()['name'],
      'date_from' => $from,
      'date_to' => $to,
      'date_generated' => date('Y-m-d H:i:s'),
      'total_tax' => $total_order_tax,
      'order_number' => $order['order_number'],
      'order_id' => $order['id'],
      'items' => []
    ]);

    foreach($items as $item) {
      array_push($array_data[$oi]['items'], [
        'product_name' => $item['product_name'],
        'quantity' => $item['quantity'],
        'unit_price' => $item['unit_price'],
        'total' => $item['total'],
        'tax_rate' => $item['tax_rate']
      ]);
    }
    
  }

  header("Content-Type: application/json");
  $json_data = json_encode($array_data);
  var_dump($array_data);
  die("");

  $stmt = $conn->prepare("INSERT INTO tax_reports (company_id, report_type, date_from, date_to, report_data, generated_by) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issssi", $company_id, $report_type, $from, $to, $json_data, $user_id);
  
  if ($stmt->execute()) {
      // Log the event
      log_tax_event($conn, $company_id, $user_id, 'tax', 'REPORT_GENERATED', 'tax_report', $conn->insert_id, "Type: {$_POST['report_type']}, Format: JSON");
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
