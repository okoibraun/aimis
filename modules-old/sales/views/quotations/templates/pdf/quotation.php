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
  <h2>Quotation #<?= $quotation['quote_number'] ?></h2>
  <p>Date: <?= $quotation['quote_date'] ?></p>
  <p>Customer: <?= htmlspecialchars($customer['company_name']) ?></p>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Unit Price</th>
        <th>Discount</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $index => $item):
        $product = get_row_by_id('sales_products', $item['product_id']);
        $subtotal = $item['quantity'] * $item['unit_price'] * (1 - $item['discount_percent'] / 100);
      ?>
      <tr>
        <td><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($product['name']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td>$<?= number_format($item['unit_price'], 2) ?></td>
        <td><?= $item['discount_percent'] ?>%</td>
        <td>$<?= number_format($subtotal, 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" align="right"><strong>Tax</strong></td>
        <td>$<?= number_format($quotation['tax_amount'], 2) ?></td>
      </tr>
      <tr>
        <td colspan="5" align="right"><strong>Total</strong></td>
        <td>$<?= number_format($quotation['total_amount'], 2) ?></td>
      </tr>
    </tfoot>
  </table>
</body>
</html>
