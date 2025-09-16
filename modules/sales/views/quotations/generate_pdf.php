<?php
// require './config/bootstrap.php';

require_once '../../../../config/db.php'; // Your database connection
require '../../includes/helpers.php';
require './lib/pdf.php';

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


// TCPDF Implementation
// require_once '../../../../vendor/tecnickcom/tcpdf/tcpdf.php';

// $quotation = $conn->query("SELECT * FROM sales_quotations WHERE id = $id")->fetch_assoc();
// $customer = $conn->query("SELECT * FROM sales_customers WHERE id = {$quotation['customer_id']}")->fetch_assoc();
// $items = $conn->query("SELECT * FROM sales_quotation_items WHERE quotation_id = $id");

// $product_item = "";

// foreach ($items as $index => $item) {
//   $product = $conn->query("SELECT * FROM sales_products WHERE id = {$item['product_id']}")->fetch_assoc();
//   $subtotal = $item['quantity'] * $item['unit_price'] * (1 - $item['discount'] / 100);
//   $sub_total = number_format($subtotal, 2);
//   $unit_price = number_format($item['unit_price'], 2);
//   $index = $index + 1;

//   $product_item .= "<tr>
//     <td>{$index}</td>
//     <td>{$product['name']}</td>
//     <td>{$item['quantity']}</td>
//     <td>N{$unit_price}</td>
//     <td>{$item['discount']}%</td>
//     <td>N{$sub_total}</td>
//   </tr>";
// }

// $pdf = new TCPDF();
// $pdf->SetCreator(PDF_CREATOR);
// $pdf->SetAuthor('AIMIS Sales');
// $pdf->SetTitle('Quotation ' . $quotation['quote_number']);
// $pdf->SetMargins(15, 20, 15);
// $pdf->AddPage();

// $logoPath = "../../../../assets/images/aimis_logo.png";
// if (file_exists($logoPath)) {
//     // $pdf->Image($logoPath, 15, $pdf->GetY(), 40);
//     $pdf->Image($logoPath, 15, 15, 65);
//     $pdf->Ln(20);
// }

// // HTML content
// $html = "
//   <!DOCTYPE html>
// <html>
// <head>
//   <meta charset='utf-8'>
//   <title>Quotation</title>
//   <style>
//     body { font-family: Arial, sans-serif; font-size: 12px; }
//     table { width: 100%; border-collapse: collapse; margin-top: 20px; }
//     th, td { border: 1px solid #444; padding: 6px; text-align: left; }
//     th { background: #eee; }
//   </style>
// </head>
// <body>
//   <h2>Quotation #{$quotation['quote_number']}</h2>
//   <p>Date: {$quotation['quotation_date']}</p>
//   <p>Customer: {$customer['name']}</p>

//   <table class='table table-striped'>
//     <thead>
//       <tr>
//         <th>#</th>
//         <th>Product</th>
//         <th>Qty</th>
//         <th>Unit Price</th>
//         <th>Discount</th>
//         <th>Subtotal</th>
//       </tr>
//     </thead>
//     <tbody>
//       {$product_item}
//       <tr>
//         <td></td>
//         <td></td>
//         <td></td>
//         <td></td>
//         <td></td>
//         <td></td>
//       </tr>
//     </tbody>
//     <tfoot>
//       <tr>
//         <td></td>
//         <td></td>
//         <td></td>
//         <td></td>
//         <td align='right'><strong>Tax</strong></td>
//         <td>N" . number_format($quotation['tax'] ?? 0, 2) . "</td>
//       </tr>
//       <tr>
//         <td></td>
//         <td></td>
//         <td></td>
//         <td></td>
//         <td align='right'><strong>Total</strong></td>
//         <td>N" .number_format($quotation['total'], 2) ."</td>
//       </tr>
//     </tfoot>
//   </table>
// </body>
// </html>
// ";

// $pdf->writeHTML($html, true, false, true, false, '');

// // Add Signature
// // ... previous $pdf->writeHTML($html)

// $pdf->Ln(10); // Add space before signature

// $signatureHTML = "
// <p><strong>Digitally Signed</strong></p>
// <p>Signed by: AIMIS Signature Authority<br>
// Signed on: " . date('Y-m-d H:i:s') . "</p>
// ";

// // Add signature image if available
// // $signaturePath = "/public/assets/img/signature.png";
// // if (file_exists($signaturePath)) {
// //     $pdf->Image($signaturePath, 15, $pdf->GetY(), 40);
// //     $pdf->Ln(20);
// // }

// $pdf->writeHTML($signatureHTML, true, false, true, false, '');

// $pdf->Output("quotation_{$quotation['quote_number']}.pdf", 'I'); // Output to browser
// exit;