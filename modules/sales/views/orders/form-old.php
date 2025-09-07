<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$id = $_GET['id'] ?? null;
$order = $items = null;

$customers = $db->query("SELECT id, name FROM sales_customers ORDER BY id");
$products  = $db->query("SELECT * FROM sales_products ORDER BY name");
$total_products = $products->num_rows;
//    $products = $get_products->fetch_all();

if ($id) {
    $order = get_row_by_id('sales_orders', $id);
    $items = $db->query("SELECT * FROM sales_order_items WHERE order_id = $id");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Orders</title>
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
                <h1>Sales Order</h1>
            </section>

            <section class="content mt-3">
                <form action="orders.php?action=save" method="post" id="orderForm">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">New Order</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Customer</label>
                                <select name="customer_id" class="form-control" required>
                                    <option value="">-- Select Customer --</option>
                                    <?php foreach ($customers as $cust): ?>
                                    <option value="<?= $cust['id'] ?>" <?= ($order && $order['customer_id'] == $cust['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cust['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Order Date</label>
                                <input type="date" name="order_date" class="form-control" value="<?= $order['order_date'] ?? date('Y-m-d') ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control" value="<?= $order['delivery_date'] ?? '' ?>">
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <?php foreach (['pending', 'confirmed', 'shipped', 'cancelled'] as $status): ?>
                                    <option value="<?= $status ?>" <?= ($order && $order['status'] == $status) ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <hr>
                            <div class="row mt-2 mb-2">
                                <div class="col-6">
                                    <h5>Order Items</h5>
                                </div>
                                <div class="col-6 text-end">
                                    <!-- <button type="button" class="btn btn-sm btn-info" id="addRowBtn">+ Add Item</button> -->
                                </div>
                            </div>

                            <table class="table table-bordered mt-2 mb-2" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Discount %</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS will populate rows -->
                                 <?php for($i=0; $i<$total_products; $i++) { ?>
                                 <tr>
                                    <td class="selectProduct">
                                        <select name="product_id[]" class="form-control">
                                            <?php foreach($products as $product) { ?>
                                            <option value="<?= $product['id'] ?>"><?= $product['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td><input type="number" name="quantity[]" class="form-control" value="<?= '1' ?>" onchange="recalcTotal()"></td>
                                    <td><input type="number" name="unit_price[]" class="form-control" step="0.01" value="<?= '0' ?>" onchange="recalcTotal()"></td>
                                    <td><input type="number" name="discount_percent[]" class="form-control" step="0.01" value="<?= '0' ?>" onchange="recalcTotal()"></td>
                                    <td class="subtotal-cell">0.00</td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); recalcTotal()">X</button></td>
                                 </tr>
                                 <?php } ?>
                            </tbody>
                            </table>

                            <hr>
                            <div class="row">
                                <div class="col-md-4 offset-md-8">
                                    <div class="form-group">
                                        <label>Tax ($)</label>
                                        <input type="number" step="0.01" name="tax_amount" class="form-control" value="<?= $order['tax_amount'] ?? '0.00' ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Total ($)</label>
                                        <input type="number" step="0.01" name="total_amount" class="form-control" id="total_amount" readonly value="<?= $order['total_amount'] ?? '0.00' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
                            </div>

                        </div>
                        <div class="card-footer text-end">
                            <div class="card-toolbar">
                                <a href="orders.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-success">Save Order</button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>


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
    <script>
        let products = <?= json_encode($products) ?>;
        let items = <?= json_encode($items ?? []) ?>;

        const addRowBtn = document.querySelector('#addRowBtn');
        addRowBtn.addEventListener('click', () => {
            addRow();
            document.querySelector('.selectProduct').innerHTML = document.querySelector('productsMenu').innerHTML;
        });

        function addRow(data = {}) {

            const row = document.createElement('tr');

            //let options = products.map(p => `<option value="${p.id}" ${data.product_id == p.id ? 'selected' : ''}>${p.name}</option>`).join('');
            

            row.innerHTML = `
                <td class="selectProduct"></td>
                <td><input type="number" name="quantity[]" class="form-control" value="${data.quantity || 1}" onchange="recalcTotal()"></td>
                <td><input type="number" name="unit_price[]" class="form-control" step="0.01" value="${data.unit_price || 0}" onchange="recalcTotal()"></td>
                <td><input type="number" name="discount_percent[]" class="form-control" step="0.01" value="${data.discount_percent || 0}" onchange="recalcTotal()"></td>
                <td class="subtotal-cell">0.00</td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); recalcTotal()">X</button></td>
            `;
            document.querySelector('#itemsTable tbody').appendChild(row);
            // recalcTotal();
        }

        function recalcTotal() {
            let total = 0;
            document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
                let qty = parseFloat(row.querySelector('[name="quantity[]"]').value) || 0;
                let price = parseFloat(row.querySelector('[name="unit_price[]"]').value) || 0;
                let discount = parseFloat(row.querySelector('[name="discount_percent[]"]').value) || 0;

                let subtotal = qty * price * (1 - discount / 100);
                row.querySelector('.subtotal-cell').textContent = subtotal.toFixed(2);
                total += subtotal;
            });

            let tax = parseFloat(document.querySelector('[name="tax_amount"]').value) || 0;
            total += tax;

            document.querySelector('#total_amount').value = total.toFixed(2);
            }

            window.onload = function () {
            if (items.length) {
                items.forEach(item => addRow(item));
            }
            //  else {
            //     addRow();
            // }

            document.querySelector('[name="tax_amount"]').addEventListener('input', recalcTotal);
        }
    </script>
  </body>
  <!--end::Body-->
</html>
