<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$products = $conn->query("SELECT * FROM sales_products WHERE company_id = $company_id");
$customers = $conn->query("SELECT * FROM sales_customers WHERE company_id = $company_id AND customer_type = 'customer'");
$leads = $conn->query("SELECT * FROM sales_customers WHERE company_id = $company_id AND customer_type = 'lead'");

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $lead_id = $_POST['lead_id'];
    $customer_id = $_POST['customer_id'];
    $wht_tax_id = $_POST['wht_tax_id'] ?? 0;
    $quote_number = $_POST['quote_number'];
    $quotation_date = $_POST['quotation_date'];
    $valid_until = $_POST['valid_until'];
    $status = $_POST['status'];
    $total = $_POST['total'];
    $tax_amount = $_POST['tax'];
    $wht_tax_amount = $_POST['wht_tax_amount'] ?? 0;
    $created_by = $_POST['created_by'];
    $notes = $_POST['notes'];

    // insert_row('sales_quotations', $input);
    $quote_stmt = $conn->prepare("
        INSERT INTO sales_quotations (lead_id, company_id, customer_id, wht_tax_id, quote_number, quotation_date, valid_until, status, total, tax, wht_tax_amount, created_by, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $quote_stmt->bind_param("iiiissssdddis", $lead_id, $company_id, $customer_id, $wht_tax_id, $quote_number, $quotation_date, $valid_until, $status, $total, $tax_amount, $wht_tax_amount, $created_by, $notes);
    $quote_stmt->execute();

    // Insert quotation items
    $quotation_id = $conn->insert_id; // Get the last inserted ID
    $product_ids = $_POST['product_id'] ?? [];
    $item_quantities = $_POST['quantity'];
    $item_unit_prices = $_POST['unit_price'];
    $item_discounts = $_POST['discount_percent'];
    $item_taxs = $_POST['item_tax'];
    $totals = $_POST['item_total'];

    // Delete Previous items before inserting/overiding
    $delete_items = $conn->query("DELETE FROM sales_quotation_items WHERE quotation_id = $quotation_id");

    if($delete_items) {
        foreach ($product_ids as $index => $product_id) {
            $product_id = $product_ids[$index];
            $quantity = floatval($item_quantities[$index]);
            $unit_price = floatval($item_unit_prices[$index]);
            $discount = floatval($item_discounts[$index]);
            $item_tax = floatval($item_taxs[$index]);
            $total = floatval($totals[$index]);
    
            if (!empty($product_id)) {
                $stmt_item = $conn->prepare("INSERT INTO sales_quotation_items (quotation_id, product_id, quantity, unit_price, discount, total, tax_rate)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $sub_total = floatval($item_sub_totals[$index]);
                $stmt_item->bind_param('iiidddd', $quotation_id, $product_id, $quantity, $unit_price, $discount, $total, $item_tax);
                if($stmt_item->execute()) header("Location: ./");
            }
        }
    }
}

$is_edit = isset($quotation);
$form_action = 'form?action=save';
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
            

            <section class="content-header mt-3 mb-3">
                <h1>
                    Create Quotation
                </h1>
            </section>
            
            <section class="content">
                <form method="POST" class="card">
                    <input type="hidden" name="company_id" value="<?= $company_id; ?>">
                    <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?= $quotation['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="created_by" value="<?= $_SESSION['user_id'] ?>">
                    <div class="card-body">

                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-2">
                                    <label>Quotation Number</label>
                                    <input type="text" name="quote_number" class="form-control" required value="<?= $quotation['quote_number'] ?? 'Q-' . time() ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="customer_id" class="form-control select2" required>
                                        <option value="">-- Select --</option>
                                        <?php foreach ($customers as $cust): ?>
                                        <option value="<?= $cust['id'] ?>">
                                            <?= htmlspecialchars($cust['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Lead (Optional)</label>
                                    <select name="lead_id" id="" class="form-control select2">
                                        <option>-- Select Lead --</option>
                                        <?php foreach($leads as $lead) { ?>
                                            <option value="<?= $lead['id']; ?>"><?= $lead['title']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col">
                                <div class="form-group">
                                    <label>Quote Date</label>
                                    <input type="date" name="quotation_date" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="date" name="valid_until" class="form-control">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-2">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <?php foreach (['draft', 'sent', 'accepted', 'rejected', 'declined'] as $status): ?>
                                        <option value="<?= $status ?>">
                                            <?= ucfirst($status) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-3 mb-3">

                        <!-- Sales Quotation Items -->
                        <div class="card mt-5 mb-5">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col">
                                        <h3 class="card-title">Quotation Items</h3>
                                    </div>
                                    <div class="col-3">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-check form-switch">
                                                    <label for="priceIncludesTax" class="form-check-label">Has WHT</label>
                                                    <input type="checkbox" class="form-check-input" id="enableWHT">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <button type="button" class="btn btn-info btn-sm" onclick="addRow()">+ Add Item</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Discount %</th>
                                            <th>Tax %</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Existing or JS-added rows -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <script>
                            function addRow(data = {}) {
                                const row = `<tr>
                                    <td>
                                        <?php  //mysqli_data_seek($products, 0); ?>
                                        <select name="product_id[]" class="form-control selectProduct" onchange="getDefaultValues()" required>
                                            <option value="">Select</option>
                                            <?php foreach($products as $product): ?>
                                                <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>" data-discount="<?= $product['discount_value'] ?>" data-taxrate="<?= $product['tax_rate'] ?>"><?= $product['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input type="number" name="quantity[]" class="form-control" value="${data.quantity || 1}" onchange="recalcTotal()"></td>
                                    <td><input type="number" name="unit_price[]" class="form-control" step="0.01" value="${data.price || 0}" onchange="recalcTotal()" readonly></td>
                                    <td><input type="number" name="discount_percent[]" class="form-control" step="0.01" value="${data.discount_value || 0}" onchange="recalcTotal()"></td>
                                    <td><input type="number" name="item_tax[]" class="form-control" readonly> </td>
                                    <td><input type="number" name="item_total[]" class="subtotal-cell form-control" readonly> </td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); recalcTotal()">X</button></td>
                                </tr>`;
                                document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', row);
                                recalcTotal();
                            }

                            // Populate price and discount for each selected product item
                            function getDefaultValues() {
                                document.querySelectorAll('.selectProduct').forEach(select => {
                                    let productId = select.value;
                                    let product = Array.from(select.options).find(opt => opt.value == productId);
                                    if (product) {
                                        let row = select.closest('tr');
                                        row.querySelector('[name="unit_price[]"]').value = product.dataset.price || 0;
                                        row.querySelector('[name="discount_percent[]"]').value = product.dataset.discount || 0;
                                        row.querySelector('[name="quantity[]"]').value = 1; // Default quantity
                                        row.querySelector('[name="item_tax[]"]').value = product.dataset.taxrate || 0; // Item Tax
                                    }
                                });
                                recalcTotal();
                            }
                        </script>

                        <!-- / Sales quotation Items -->

                        <div class="row">
                            <div class="col-md-5 offset-md-7">
                                <div class="row" id="showWHT">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label>WHT Tax:</label>
                                            <select name="wht_tax_id" id="selectWHT"  class="form-control" required>
                                                <option value="<?= intval('0') ?>" selected>-- Select --</option>
                                            <?php
                                                $tax_type = $conn->query("SELECT * FROM tax_config WHERE tax_type = 'WHT' AND company_id = $company_id");
                                                foreach($tax_type as $tax) {
                                            ?>
                                                <option value="<?= $tax['id'] ?>" data-whtrate="<?= $tax['rate'] ?>" <?= ($order['wht_tax_id'] ?? '') == $tax['id'] ? 'selected' : '' ?>>
                                                <?= $tax['tax_type'] ?> - <?= $tax['description'] ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="wht_tax_amount">WHT Tax Amount (N)(%)</label>
                                            <input type="hidden" id="whtRate">
                                            <input type="number" name="wht_tax_amount" id="whtTaxAmount" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label>VAT Tax Amount (N)(%)</label>
                                            <input type="number" step="0.01" name="tax" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>Total Amount (N)</label>
                                            <input type="number" step="0.01" name="total" class="form-control" id="total" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="4"></textarea>
                        </div>

                    </div>

                    <div class="card-footer">
                        <div class="form-group float-end">
                            <a href="./" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Quotation</button>
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
    <script>
        
        let items = <?= json_encode($items ?? []) ?>;

        function recalcTotal() {
            let total = 0;
            let item_tax_rate = 0;
            const whtRate = document.querySelector('#whtRate');
            let whtTaxAmount = document.querySelector('[name="wht_tax_amount"]');

            document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
                let qty = parseFloat(row.querySelector('[name="quantity[]"]').value) || 0;
                let price = parseFloat(row.querySelector('[name="unit_price[]"]').value) || 0;
                let discount = parseFloat(row.querySelector('[name="discount_percent[]"]').value) || 0;
                let item_tax = parseFloat(row.querySelector('[name="item_tax[]').value) || 0;

                let taxrate = qty * item_tax * (price / 100);
                let subtotal = qty * price * (1 - discount / 100);
                // row.querySelector('.subtotal-cell').textContent = subtotal.toFixed(2);
                row.querySelector('.subtotal-cell').value = subtotal.toFixed(2);
                total += subtotal;
                item_tax_rate += taxrate;
            });

            document.querySelector('[name="tax"]').value = item_tax_rate;
            let tax = parseFloat(document.querySelector('[name="tax"]').value) || 0;

            whtTaxAmount.value = whtRate.value * (total / 100);

            total += tax + parseFloat(whtTaxAmount.value);

            document.querySelector('#total').value = total.toFixed(2);
        }

        window.onload = function () {
            if (items.length) {
                items.forEach(item => addRow(item));
            }
            //  else {
            //     addRow();
            // }

            document.querySelector('[name="tax"]').addEventListener('input', recalcTotal);
        }

        // Populate price and discount for each selected product item

        // Toggle WHT Tax
        const showWHT = document.querySelector("#showWHT");
        const enableWHT = document.querySelector('#enableWHT');

        // Get WHT Rate controls
        const selectWHT = document.querySelector("#selectWHT"); // Select WHR Rate
        const whtRate = document.querySelector('#whtRate'); //Set WHT Rate
        let whtTaxAmount = document.querySelector('[name="wht_tax_amount"]');

        showWHT.style.visibility = "hidden";
        enableWHT.addEventListener('change', () => {
            if(enableWHT.checked) {
                showWHT.style.visibility = "visible";
            } else {
                showWHT.style.visibility = "hidden";
                selectWHT.value = 0;
                whtRate.value = '';
                whtTaxAmount.value = '';
            }
        });
        
        // set WHT rate value
        selectWHT.addEventListener('change', () => {
            if(selectWHT.options[selectWHT.selectedIndex].value != 0) {
                whtRate.value = selectWHT.options[selectWHT.selectedIndex].dataset.whtrate;
                recalcTotal();
            } else {
                whtRate.value = '';
                recalcTotal();
            }
        });
        
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
