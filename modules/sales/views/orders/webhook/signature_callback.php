<?php
require_once '../../../../../config/db.php'; // Your database connection

$data = json_decode(file_get_contents('php://input'), true);

$orderId = $data['order_id'] ?? null;
$status = $data['status'] ?? null; // 'signed', 'rejected'

if (!$orderId || !$status) {
  http_response_code(400);
  exit('Invalid payload.');
}

$allowed = ['signed', 'rejected', 'error'];
if (!in_array($status, $allowed)) {
  http_response_code(422);
  exit('Invalid status.');
}

$db->update('sales_orders', [
    'signature_status' => $status,
    'signature_file' => $data['signed_file_url'] ?? null
], 'id = ?', [$orderId]);

echo "Webhook processed.";
