<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $invoice_date = $_POST['invoice_date'];
    $due_date = $_POST['due_date'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $total_amount = floatval($_POST['total_amount']);

    $invoice_number = 'INV-' . date('YmdHis');

    // Insert invoice
    $stmt = $conn->prepare("INSERT INTO invoices (company_id, user_id, employee_id, invoice_no, customer_id, invoice_date, due_date, description, amount)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iiisisssd', $company_id, $user_id, $employee_id, $invoice_number, $customer_id, $invoice_date, $due_date, $description, $total_amount);
    // $stmt->execute();
    if ($stmt->execute()) {
        $invoice_id = $stmt->insert_id;

        // Insert invoice items
        $item_descriptions = $_POST['item_description'];
        $item_quantities = $_POST['item_quantity'];
        $item_unit_prices = $_POST['item_unit_price'];
        $item_discounts = $_POST['item_discount'];
        $item_tax_rates = $_POST['item_tax_rate'];
        $item_sub_totals = $_POST['item_sub_total'];
        $product_ids = $_POST['product_id'];

        foreach ($item_descriptions as $index => $description) {
            if (!empty($description)) {
              $quantity = floatval($item_quantities[$index]);
              $unit_price = floatval($item_quantities[$index]);
              $discount = floatval($item_discounts[$index]);
              $tax_rate = floatval($item_tax_rates[$index]);
              $sub_total = floatval($item_sub_totals[$index]);
              $product_id = $product_ids[$index];

              $stmt_item = $conn->prepare("INSERT INTO invoice_items (invoice_id, product_id, description, quantity, unit_price, discount, tax_rate, total)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)
              ");
              
              $stmt_item->bind_param('iisddddd', $invoice_id, $product_id, $description, $quantity, $unit_price, $discount, $tax_rate, $sub_total);
              $stmt_item->execute();
            }
        }

        $success = true;
    } else {
        echo "Error: " . $stmt->error;
    }
}

if(in_array($_SESSION['user_role'], system_users())) {
  $customers = $conn->query("SELECT id, name FROM sales_customers ORDER BY name");
  $products = $conn->query("SELECT * FROM sales_products ORDER BY name");
} else if(in_array($_SESSION['user_role'], super_roles())) {
  $customers = $conn->query("SELECT id, name FROM sales_customers WHERE company_id = $company_id ORDER BY name");
  $products = $conn->query("SELECT * FROM sales_products WHERE company_id = $company_id ORDER BY name");
} else {
  $customers = $conn->query("SELECT id, name FROM sales_customers WHERE company_id = $company_id AND user_id = $user_id OR employee_id = $employee_id ORDER BY name");
  $products = $conn->query("SELECT * FROM sales_products WHERE company_id = $company_id AND user_id = $user_id OR employee_id = $employee_id ORDER BY name");
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Add Invoice</title>
    <?php include_once("../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

          <section class="content-header mt-3 mb-3">
            <h1>Add New Invoice</h1>
          </section>

          <section class="content">
            <?php if ($success): ?>
              <div class="alert alert-success">Invoice created successfully.</div>
            <?php endif; ?>

            <div class="card">
              <div class="card-body">
                <form method="POST">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">Invoice Details</h3>
                        </div>
                        <div class="card-body">
                          <div class="form-group">
                            <label>Customer</label>
                            <select name="customer_id" class="form-control" required>
                              <option value="">-- Select Customer --</option>
                              <?php foreach($customers as $customer) { ?>
                                <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                              <?php } ?>
                            </select>
                          </div>
        
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Invoice Date</label>
                                <input type="date" name="invoice_date" value="<?= date('Y-m-d') ?>" class="form-control" required>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Due Date</label>
                                <input type="date" name="due_date" class="form-control" required>
                              </div>
                            </div>
                          </div>
                          
                          <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Tuition, Library Fee, etc."></textarea>
                          </div>
        
                          <div class="form-group">
                            <label>Total Amount</label>
                            <input type="number" name="total_amount" id="total_amount" step="0.01" class="form-control" required readonly>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <!-- Sales Invoice Items -->
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">Invoice Items</h3>
                          <div class="card-tools">
    
                            <button type="button" class="btn btn-sm btn-info float-right" id="addItem">
                              <i class="bi bi-plus"></i>
                               Add Item
                            </button>
                          </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                          <table class="table table-bordered" id="itemsTable">
                            <thead>
                              <tr>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Discount</th>
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
                        document.getElementById('addItem').addEventListener('click', function() {
                          let row = `
                          <tr>
                              
                              <td>
                                <select name="product_id[]" class="form-control productSelect select2" onchange="insertDefaultValues()">
                                  <option value="">-- Select Product --</option>
                                  <?php foreach($products as $product) { ?>
                                  <option value="<?= $product['id']; ?>" data-price="<?= $product['price']; ?>" data-discount="<?= $product['discount_value']; ?>"><?= $product['name']; ?></option>
                                  <?php } ?>
                                </select>
                              </td>
                              <td><input type="text" name="item_description[]" class="form-control" required></td>
                              <td><input type="number" step="1" name="item_quantity[]" class="form-control" value="1" onchange="reCalc()"></td>
                              <td><input type="number" step="0.01" name="item_unit_price[]" class="form-control" onchange="reCalc()"></td>
                              <td><input type="number" step="0.01" name="item_discount[]" class="form-control" value="0" onchange="reCalc()"></td>
                              <td><input type="number" step="0.01" name="item_tax_rate[]" class="form-control" value="0"></td>
                              <td><input type="number" step="0.01" name="item_sub_total[]" class="form-control" readonly></td>
                              <td><button type="button" class="btn btn-danger btn-sm removeItem">&times;</button></td>
                          </tr>
                          `;
                          document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', row);
                          reCalc(); // Recalculate totals after adding new row
                        });
    
                        function insertDefaultValues() {
                          // Get all product selects and update their values
                          document.querySelectorAll('.productSelect').forEach(select => {
                            let price = select.options[select.selectedIndex].dataset.price;
                            let discount = select.options[select.selectedIndex].dataset.discount;
    
                            if(price) {
                              let row = select.closest('tr');
                              
                              row.querySelector('input[name="item_unit_price[]"]').value = price || 0;
                              row.querySelector('input[name="item_discount[]"]').value = discount || 0;
                            }
                          });
                          reCalc(); // Recalculate totals after setting default values
                        }
    
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('removeItem')) {
                                e.target.closest('tr').remove();
                                reCalc(); // Recalculate totals after removing item
                            }
                        });
                      </script>
                      <!-- / Sales Invoice Items -->
                    </div>

                  </div>


                  <div class="form-group float-end">
                    <?= ($success) ? '<a href="./" class="btn btn-danger"> Close</a>' : '<a href="./" class="btn btn-danger"> Cancel</a>'; ?>
                    <button type="submit" class="btn btn-primary">Create Invoice</button>
                  </div>
                </form>
              </div>
            </div>
          </section>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <script>
      function reCalc() {
        // Recalculate total
        let total = 0;
        document.querySelectorAll('#itemsTable tbody tr').forEach(row => {

          let quantity = parseFloat(row.querySelector('input[name="item_quantity[]"]').value) || 0;
          let unitPrice = parseFloat(row.querySelector('input[name="item_unit_price[]"]').value) || 0;
          let discountValue = parseFloat(row.querySelector('input[name="item_discount[]"]').value) || 0;
          let subTotal = quantity * unitPrice * (1 - discountValue / 100);
          row.querySelector('input[name="item_sub_total[]"]').value = subTotal.toFixed(2);
          total += subTotal;
        });

        let tax = parseFloat(document.querySelector('[name="item_tax_rate[]"]').value) || 0;
        total += tax;

        document.querySelector('#total_amount').value = total.toFixed(2);
      }
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
