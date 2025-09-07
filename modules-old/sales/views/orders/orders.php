<?php
require_once '../../../../config/db.php';
require_once '../../includes/helpers.php';
require_once '../../../../functions/sales/orders.php';

$action = $_GET['action'] ?? 'list';
$form_action = (isset($_GET['id']) && $_GET['id']) ? "form.php?id={$_GET['id']}" : 'form.php';

switch ($action) {
  case 'list':
    redirect('list.php');
    break;

  case 'form':
    redirect("{$form_action}");
    break;

  case 'save':
    handle_order_form_submission();
    break;

  case 'pdf':
    require_once '../vendor/tcpdf/tcpdf.php';

    $id = $_GET['id'] ?? null;
    if (!$id) exit('Missing ID');

    $order = $db->query("SELECT o.*, c.company_id, c.address, c.email, c.phone
        FROM sales_orders o
        JOIN sales_customers c ON o.customer_id = c.id
        WHERE o.id = $id")->fetch_assoc();

    if (!$order) exit("Order not found.");

    $items = $db->query("SELECT i.*, p.name AS product_name
        FROM sales_order_items i
        JOIN sales_products p ON i.product_id = p.id
        WHERE i.order_id = $id");

    $pdf = new TCPDF();
    $pdf->SetCreator('AIMIS');
    $pdf->SetTitle("Invoice #{$order['order_number']}");
    $pdf->AddPage();

    ob_start();
    include 'pdf_template.php'; // Include your PDF template file
    break;

  case 'sign':
    require_once '../vendor/tcpdf/tcpdf.php';

    $id = $_GET['id'] ?? null;
    if (!$id) exit('Missing ID');

    $order = $db->fetch("SELECT o.*, c.company_name, c.address, c.email, c.phone
        FROM sales_orders o
        JOIN sales_customers c ON o.customer_id = c.id
        WHERE o.id = ?", [$id]);

    if (!$order) exit("Order not found.");

    $items = $db->fetchAll("SELECT i.*, p.name AS product_name
        FROM sales_order_items i
        JOIN sales_products p ON i.product_id = p.id
        WHERE i.order_id = ?", [$id]);

    $pdf = new TCPDF();
    $pdf->SetCreator('AIMIS');
    $pdf->SetTitle("Signed Invoice #{$order['order_number']}");
    $pdf->AddPage();

    ob_start();
    include 'pdf_sign_template.php'; // Include your PDF sign template file
    break;

  case 'request_signature':
    $id = $_GET['id'] ?? null;
    if (!$id) exit('Missing ID');

    $order = $db->query("SELECT * FROM sales_orders WHERE id = $id")->fetch_assoc();
    if (!$order) exit('Order not found.');

    // Simulate sending to external API
    $signatureRequestId = uniqid("sign_");

    // Store status
    $db->update('sales_orders', [
        'signature_status' => 'pending',
      // 'signature_file' => 'optional/file/path.pdf'
    ], 'id = ?', [$id]);

    echo "<p>Signature request sent. Awaiting webhook response.</p>";
    exit;
    break;

  case 'delete':
    delete_order($_GET['id']);
    break;

  default:
    redirect('list.php');
}
