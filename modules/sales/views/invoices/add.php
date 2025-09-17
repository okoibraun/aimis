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
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$customers = $db->query("SELECT id, name FROM sales_customers WHERE company_id = $company_id AND customer_type = 'customer'");
$leads = $conn->query("SELECT * FROM sales_customers WHERE company_id = $company_id AND customer_type = 'lead'");
$orders = $db->query("SELECT * FROM sales_orders WHERE company_id = $company_id");
$quotations = $conn->query("SELECT * FROM sales_quotations WHERE company_id = $company_id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_number = $_POST['invoice_number'];
    $invoice_date = $_POST['invoice_date'];
    $due_date = $_POST['due_date'];
    $customer_id = $_POST['customer_id'];
    $order_id = $_POST['order_id'] ?? 0;
    $quotation_id = $_POST['quotation_id'];
    $lead_id = $_POST['lead_id'];
    $total_amount = $_POST['total_amount'];
    $vat_tax_amount = $_POST['vat_tax_amount'];
    $wht_tax_amount = $_POST['wht_tax_amount'];
    $notes = $_POST['notes'];

    $insert = $db->query("INSERT INTO sales_invoices (company_id, user_id, employee_id, invoice_number, invoice_date, due_date, customer_id, order_id, quotation_id, lead_id, total_amount, vat_tax_amount, wht_tax_amount, notes, status) VALUES ($company_id,
        $user_id,
        $employee_id,
        '$invoice_number',
        '$invoice_date',
        '$due_date',
        $customer_id,
        $order_id,
        $quotation_id,
        $lead_id,
        $total_amount,
        $vat_tax_amount,
        $wht_tax_amount,
        '$notes', 'unpaid')
    ");

    if($insert) {
      // Log activity
      include('../../../../functions/log_functions.php');
      log_activity($user_id, $company_id, 'create_invoice', "Created invoice #{$invoice_number}");
      $_SESSION['success'] = "Invoice created successfully.";
      redirect('./');
    }
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Invoices</title>
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

          <div class="content-wrapper">
            <section class="content-header mt-3 mb-3">
                <h1>Create Invoice</h1>
            </section>

            <section class="content">
              <form method="post" class="card">
                <div class="card-header">
                  <h3 class="card-title">New Invoice</h3>
                  <div class="card-tools">
                    <div class="form-check form-switch">
                        <label for="enable_wht" class="form-check-label">Has WHT</label>
                        <input type="checkbox" class="form-check-input" id="enableWHT">
                    </div>
                  </div>
                </div>
                <div class="card-body">

                    <div class="form-group mt-3">
                    <label for="invoice_number">Invoice Number</label>
                    <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="<?= "I-".time(); ?>" required readonly>
                    </div>

                    <div class="row mt-3">
                      <div class="col">
                        <div class="form-group">
                          <label for="invoice_date">Invoice Date</label>
                          <input type="date" name="invoice_date" id="invoice_date" class="form-control" required>
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="due_date">Due Date</label>
                          <input type="date" name="due_date" id="due_date" class="form-control">
                        </div>
                      </div>
                    </div>

                    <div class="row mt-3">
                      <div class="col">
                        <div class="form-group">
                            <label for="customer_id">Customer (Optional)</label>
                            <select name="customer_id" id="customer_id" class="form-control select2" required>
                                <option value="<?= intval("0"); ?>">-- Select Customer --</option>
                                <?php foreach ($customers as $cust): ?>
                                <option value="<?= $cust['id'] ?>"><?= $cust['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label for="lead_id">Lead (Optional)</label>
                          <select name="lead_id" id="lead_id" class="form-control select2">
                              <option value="<?= intval("0"); ?>">-- Select Lead --</option>
                              <?php foreach ($leads as $lead): ?>
                              <option value="<?= $lead['id'] ?>"><?= $lead['title'] ?></option>
                              <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row mt-3">
                      <div class="col">
                        <div class="form-group">
                            <label for="quotation_id">Linked Quotation (Optional)</label>
                            <select name="quotation_id" id="selectQuotation" class="form-control select2" onchange="getQuoteValues()" required>
                                <option value="<?= intval("0"); ?>" selected>-- Select Quotation --</option>
                                <?php foreach ($quotations as $quotation): ?>
                                <option value="<?= $quotation['id'] ?>" data-tax="<?= $quotation['tax']; ?>" data-whttaxamount="<?= $quotation['wht_tax_amount']; ?>" data-total="<?= $quotation['total']; ?>"><?= $quotation['quote_number'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                      </div>

                      <div class="col">
                        <div class="form-group">
                          <label for="order_id">Linked Order (Optional)</label>
                          <select name="order_id" id="selectOrder" class="form-control select2" onchange="getOrderValues()">
                              <option value="<?= intval("0"); ?>" selected>-- Select Order --</option>
                              <?php foreach ($orders as $ord): ?>
                              <option value="<?= $ord['id'] ?>" data-taxamount="<?= $ord['vat_tax_amount']; ?>" data-whttaxamount="<?= $ord['wht_tax_amount']; ?>" data-totalamount="<?= $ord['total_amount']; ?>"><?= $ord['order_number'] ?></option>
                              <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3 offset-md-9">
                            <div class="row" id="showWHT">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="wht_tax_amount">WHT Tax Amount (N)(%)</label>
                                        <input type="number" name="wht_tax_amount" id="whtRate" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>VAT Tax Amount (N)(%)</label>
                                <input type="number" step="0.01" name="vat_tax_amount" class="form-control" value="<?= $invoice['vat_tax_amount'] ?? '0.00' ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Total Amount (N)</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control" id="total_amount" value="<?= $invoice['total_amount'] ?? '0.00' ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="4"></textarea>
                    </div>

                </div>

                <div class="card-footer">
                    <div class="form-group float-end">
                        <a href="./" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Invoice</button>
                    </div>
                </div>
              </form>
            </section>
          </div>

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
        // Populate price and discount for each selected product item
        function getQuoteValues() {
            document.querySelectorAll('#selectQuotation').forEach(select => {
                let quotationId = select.value;
                let quotation = Array.from(select.options).find(opt => opt.value == quotationId);

                if (quotation) {
                    // Sets Order select menu to default if it was previously selected
                    document.querySelector('#selectOrder').value = 0;
                    // document.querySelector('#selectOrder').selectedIndex = 0;
                    // Sets total_amount and tax_amount input field's value
                    document.querySelector('[name="vat_tax_amount"]').value = quotation.dataset.tax || 0
                    document.querySelector('[name="total_amount"]').value = quotation.dataset.total || 0;
                    document.querySelector('[name="wht_tax_amount"]').value = quotation.dataset.whttaxamount;
                }
            });
        }

        function getOrderValues() {
            document.querySelectorAll('#selectOrder').forEach(select => {
                let orderId = select.value;
                let order = Array.from(select.options).find(opt => opt.value == orderId);

                if (order) {
                    // Sets Quotation select menu to default if it was previously selected
                    document.querySelector('#selectQuotation').value = 0
                    // document.querySelector('#selectQuotation').selectedIndex = 0
                    // Sets total_amount and tax_amount input field's value
                    document.querySelector('[name="vat_tax_amount"]').value = order.dataset.taxamount || 0
                    document.querySelector('[name="total_amount"]').value = order.dataset.totalamount || 0;
                    document.querySelector('[name="wht_tax_amount"]').value = order.dataset.whttaxamount;
                }
            });
        }
        
        // Toggle WHT Tax
        const showWHT = document.querySelector("#showWHT");
        const enableWHT = document.querySelector('#enableWHT');

        // Get WHT Rate controls
        const whtRate = document.querySelector('#whtRate'); //Set WHT Rate

        showWHT.style.visibility = "hidden";
        enableWHT.addEventListener('change', () => {
            if(enableWHT.checked) {
                showWHT.style.visibility = "visible";
            } else {
                showWHT.style.visibility = "hidden";
                whtRate.value = '';
            }
        });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
