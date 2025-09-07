<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$id = $_GET['id'] ?? null;
$quotation = $id ? get_row_by_id('sales_quotations', $id) : null;
$products = get_all_rows('sales_products', 'name ASC');
$customers = get_all_rows('sales_customers', 'company_name ASC');

$is_edit = isset($quotation);
$form_action = 'form.php?action=save';
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Quotations</title>
    <?php include_once("../../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">
            

            <section class="content-header">
                <h1><?= $is_edit ? 'Edit' : 'Create' ?> Quotation</h1>
            </section>

            <section class="content">
                <form action="<?= $form_action ?>" method="POST">
                    <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?= $quotation['id'] ?>">
                    <?php endif; ?>

                    <div class="row">
                    <div class="col-md-6">
                        <!-- Basic Info -->
                        <div class="form-group">
                        <label>Quotation Number</label>
                        <input type="text" name="quote_number" class="form-control" required
                                value="<?= $quotation['quote_number'] ?? 'Q-' . time() ?>">
                        </div>

                        <div class="form-group">
                        <label>Customer</label>
                        <select name="customer_id" class="form-control" required>
                            <option value="">-- Select --</option>
                            <?php foreach ($customers as $cust): ?>
                            <option value="<?= $cust['id'] ?>"
                                <?= isset($quotation['customer_id']) && $quotation['customer_id'] == $cust['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cust['company_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        </div>

                        <div class="form-group">
                        <label>Lead (Optional)</label>
                        <input type="number" name="lead_id" class="form-control"
                                value="<?= $quotation['lead_id'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                        <label>Quote Date</label>
                        <input type="date" name="quote_date" class="form-control"
                                value="<?= $quotation['quote_date'] ?? date('Y-m-d') ?>">
                        </div>

                        <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control"
                                value="<?= $quotation['expiry_date'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <?php foreach (['Draft', 'Sent', 'Accepted', 'Rejected', 'Declined'] as $status): ?>
                            <option value="<?= $status ?>" <?= ($quotation['status'] ?? 'Draft') == $status ? 'selected' : '' ?>>
                                <?= $status ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        </div>

                        <input type="hidden" name="created_by" value="<?= $_SESSION['user_id'] ?>">
                    </div>

                    <div class="col-md-6">
                        <!-- Line Items -->
                        <h5>Quotation Items</h5>
                        <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Discount %</th>
                            <th>Subtotal</th>
                            <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addItemRow()">Add Item</button>

                        <div class="mt-3">
                        <label>Tax Amount</label>
                        <input type="number" step="0.01" name="tax_amount" class="form-control" id="taxAmount" value="0.00">
                        <label>Total Amount</label>
                        <input type="number" step="0.01" name="total_amount" class="form-control" id="totalAmount" readonly>
                        </div>
                    </div>
                    </div>

                    <div class="mt-4">
                    <button type="submit" class="btn btn-success">Save Quotation</button>
                    <a href="quotations.php" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </section>

            <script>
                const products = <?= json_encode($products) ?>;

                function addItemRow() {
                    const table = document.querySelector("#itemsTable tbody");
                    const row = document.createElement('tr');

                    row.innerHTML = `
                        <td>
                        <select name="items[][product_id]" class="form-control">
                            ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                        </select>
                        </td>
                        <td><input type="number" name="items[][quantity]" class="form-control qty" value="1" min="1"></td>
                        <td><input type="number" name="items[][unit_price]" class="form-control price" step="0.01" value="0.00"></td>
                        <td><input type="number" name="items[][discount_percent]" class="form-control discount" step="0.01" value="0"></td>
                        <td><input type="number" class="form-control subtotal" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); calculateTotals();">X</button></td>
                    `;

                    table.appendChild(row);
                    row.querySelectorAll('input').forEach(i => i.addEventListener('input', calculateTotals));
                    calculateTotals();
                }

                function calculateTotals() {
                    let total = 0;
                    document.querySelectorAll("#itemsTable tbody tr").forEach(row => {
                        const qty = parseFloat(row.querySelector(".qty").value) || 0;
                        const price = parseFloat(row.querySelector(".price").value) || 0;
                        const discount = parseFloat(row.querySelector(".discount").value) || 0;
                        const subtotal = qty * price * (1 - discount / 100);
                        row.querySelector(".subtotal").value = subtotal.toFixed(2);
                        total += subtotal;
                    });
                    const tax = parseFloat(document.querySelector("#taxAmount").value) || 0;
                    document.querySelector("#totalAmount").value = (total + tax).toFixed(2);
                }

                document.querySelector("#taxAmount").addEventListener('input', calculateTotals);
            </script>
            

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
