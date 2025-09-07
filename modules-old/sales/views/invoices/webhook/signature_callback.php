<?php
require_once '../../../includes/helpers.php'; // Include your helper functions

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../../../login.php');
    exit();
}

$invoice_id = $_POST['invoice_id'] ?? null;
$status = $_POST['status'] ?? null;
$signed_pdf_url = $_POST['signed_pdf_url'] ?? null;

if (!$invoice_id || !$status) {
    http_response_code(400);
    exit('Invalid payload');
}

// Download signed PDF
$filePath = null;
if ($signed_pdf_url && $status === 'signed') {
    $pdfData = file_get_contents($signed_pdf_url);
    $filePath = "/uploads/signed/invoice_{$invoice_id}_" . time() . ".pdf";
    file_put_contents(BASE_PATH . $filePath, $pdfData);
}

$db->update('sales_invoice_signatures', [
    'status' => $status,
    'signed_file_path' => $filePath,
    'signed_at' => date('Y-m-d H:i:s'),
    'webhook_response' => json_encode($_POST)
], ['invoice_id' => $invoice_id]);

$db->update('sales_invoices', ['status' => 'signed'], ['id' => $invoice_id]);

echo "OK";
