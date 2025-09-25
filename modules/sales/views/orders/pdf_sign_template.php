<?php $logo = $conn->query("SELECT logo FROM companies WHERE id = $company_id")->fetch_assoc()['logo']; ?>
<img src="/uploads/company/<?= htmlspecialchars($logo) ?>" alt="Company Logo" height="107">
<h1>Order #: <?= $order['order_number'] ?> (Signed)</h1>
<p><strong>Date:</strong> <?= $order['order_date'] ?></p>

<h3>Customer:</h3>
<p>
  <?= htmlspecialchars($order['customer_name']) ?><br>
  <?= nl2br(htmlspecialchars($order['address'])) ?><br>
  <?= htmlspecialchars($order['email']) ?><br>
  <?= htmlspecialchars($order['phone']) ?>
</p>

<hr>

<table border="1" cellpadding="4" cellspacing="0" width="100%">
  <thead>
    <tr style="background:#eee;">
      <th><strong>Product</strong></th>
      <th><strong>Qty</strong></th>
      <th><strong>Unit Price</strong></th>
      <th><strong>Discount %</strong></th>
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

<?php
    $html = ob_get_clean();
    $pdf->writeHTML($html, true, false, true, false, '');

    // ➕ Apply simulated signature
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 102, 204); // blue
    $pdf->SetXY(15, 260);
    $pdf->Cell(0, 10, "Digitally Signed by AIMIS System", 0, 1, 'L');

    $pdf->Output("signed_invoice_{$order['order_number']}.pdf", 'I');
    exit;
?>