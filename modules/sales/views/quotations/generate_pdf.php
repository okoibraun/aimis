<?php
// require './config/bootstrap.php';

require_once '../../../../config/db.php'; // Your database connection
require '../../includes/helpers.php';
require './lib/pdf.php';
$company_id = $_SESSION['company_id'];

$type = $_GET['type'] ?? null;
$id = $_GET['id'] ?? null;

if (!$type || !$id) {
  exit("Invalid PDF request.");
}

//DOMPDF Implementation
ob_start();

switch ($type) {
  case 'quotation':
    $quotation = get_row_by_id('sales_quotations', $id);
    $customer = get_row_by_id('sales_customers', $quotation['customer_id']);
    $items = $conn->query("SELECT * FROM sales_quotation_items WHERE quotation_id = $id");
    include './templates/pdf/quotation.php';
    break;

  default:
    exit("Unknown document type.");
}

$html = ob_get_clean();
generate_pdf($html, strtoupper($type) . "_$id.pdf");