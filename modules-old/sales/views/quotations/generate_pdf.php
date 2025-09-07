<?php
require './config/bootstrap.php';
require './lib/pdf.php';

$type = $_GET['type'] ?? null;
$id = $_GET['id'] ?? null;

if (!$type || !$id) {
  exit("Invalid PDF request.");
}

ob_start();

switch ($type) {
  case 'quotation':
    $quotation = get_row_by_id('sales_quotations', $id);
    $customer = get_row_by_id('sales_customers', $quotation['customer_id']);
    $items = get_all_by_field('sales_quotation_items', 'quotation_id', $id);
    include './templates/pdf/quotation.php';
    break;

  default:
    exit("Unknown document type.");
}

$html = ob_get_clean();
generate_pdf($html, strtoupper($type) . "_$id.pdf");
