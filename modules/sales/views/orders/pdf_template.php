<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Quotation</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #444; padding: 6px; text-align: left; }
    th { background: #eee; }
  </style>
</head>
<body>
<?php $logo = $conn->query("SELECT logo FROM companies WHERE id = $company_id")->fetch_assoc()['logo']; ?>
<img src="/uploads/company/<?= htmlspecialchars($logo) ?>" alt="Company Logo" height="107">
<h1>Order #: <?= $order['order_number'] ?></h1>
<p><strong>Date:</strong> <?= $order['order_date'] ?></p>

<h3>Customer:</h3>
<p>
  <strong><?= htmlspecialchars($order['customer_name']) ?></strong><br>
  <?= nl2br(htmlspecialchars($order['address'])) ?>,<br>
  <?= htmlspecialchars($order['email']) ?><br>
  <?= htmlspecialchars($order['phone']) ?>
</p>

<hr>

<table border="1" cellpadding="4" cellspacing="0">
  <thead>
    <tr style="background:#eee;">
      <th><strong>Product</strong></th>
      <th><strong>Qty</strong></th>
      <th><strong>Unit Price</strong></th>
      <th><strong>Discount %</strong></th>
      <!-- <th><strong>Tax Rate %</strong></th> -->
      <th><strong>Subtotal</strong></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $total = 0;
    foreach ($items as $item):
      $subtotal = $item['quantity'] * $item['unit_price'] * (1 - $item['discount_percent'] / 100);
      $total += $subtotal;
    ?>
    <tr>
      <td><?= htmlspecialchars($item['product_name']) ?></td>
      <td><?= $item['quantity'] ?></td>
      <td>N<?= number_format($item['unit_price'], 2) ?></td>
      <td><?= $item['discount_percent'] ?>%</td>
      <!-- <td><?= $item['tax_rate'] ?>%</td> -->
      <td>N<?= number_format($subtotal, 2) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h3>Total Summary:</h3>
<p>Subtotal: N<?= number_format($total, 2) ?></p>
<p>Tax: N<?= number_format($order['vat_tax_amount'], 2) ?></p>
<h2>Total: N<?= number_format($order['total_amount'], 2) ?></h2>

<?php if (!empty($order['notes'])): ?>
<h4>Notes:</h4>
<p><?= nl2br($order['notes']) ?></p>
<?php endif; ?>

</body>
</html>

<?php
    $html = ob_get_clean();
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("invoice_{$order['order_number']}.pdf", 'I'); // I = inline preview
    exit;
?>