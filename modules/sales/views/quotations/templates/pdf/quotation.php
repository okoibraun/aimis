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
  <img src="http://aimis.test/uploads/company/<?= htmlspecialchars($logo) ?>" alt="Company Logo" height="107">
  <h2>Quotation #<?= $quotation['quote_number'] ?></h2>
  <p>Date: <?= $quotation['quotation_date'] ?></p>
  <p>Customer: <?= htmlspecialchars($customer['name']) ?></p>

  <table class="table table-striped">
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
        $product = $conn->query("SELECT * FROM sales_products WHERE id = {$item['product_id']}")->fetch_assoc();
        $subtotal = $item['quantity'] * $item['unit_price'] * (1 - $item['discount'] / 100);
      ?>
      <tr>
        <td><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($product['name']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td>N<?= number_format($item['unit_price'], 2) ?></td>
        <td><?= $item['discount'] ?>%</td>
        <td>N<?= number_format($subtotal, 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" align="right"><strong>Tax</strong></td>
        <td>N<?= number_format($quotation['tax'] ?? 0, 2) ?></td>
      </tr>
      <tr>
        <td colspan="5" align="right"><strong>Total</strong></td>
        <td>N<?= number_format($quotation['total'], 2) ?></td>
      </tr>
    </tfoot>
  </table>
</body>
</html>
