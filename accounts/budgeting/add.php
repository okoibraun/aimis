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


$budget_added = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $budget_name = mysqli_real_escape_string($conn, $_POST['budget_name']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $total_amount = floatval($_POST['total_amount']);
    $created_year = date('Y');
    $company_id = $_SESSION['company_id'];

    // Insert the budget into the database
    $stmt = mysqli_prepare($conn, "
        INSERT INTO budgets (company_id, user_id, employee_id, budget_name, start_date, end_date, total_amount, created_year)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'iiisssds', $company_id, $user_id, $employee_id, $budget_name, $start_date, $end_date, $total_amount, $created_year);
    $budget_added = mysqli_stmt_execute($stmt);

    if( $budget_added ) {
        // Insert budget items
        $budget_id = mysqli_insert_id($conn);
        $item_descriptions = $_POST['item_description'];
        $item_quantities = $_POST['item_quantity'];
        $item_unit_prices = $_POST['item_unit_price'];
        $item_discounts = $_POST['item_discount'];
        $item_sub_totals = $_POST['item_sub_total'];
        $budget_items = $_POST['budget_items'];

        foreach ($item_descriptions as $index => $description) {
            if (!empty($description)) {
              $quantity = $item_quantities[$index];
              $unit_price = $item_unit_prices[$index];
              $discount = $item_discounts[$index];
              $sub_total = $item_sub_totals[$index];
              $budget_item = $budget_items[$index];

              $stmt_item = mysqli_prepare($conn, "INSERT INTO budget_items (company_id, user_id, employee_id, budget_id, budget_item, description, quantity, unit_price, discount, total)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
              ");
              mysqli_stmt_bind_param($stmt_item, 'iiiissdddd', $company_id, $user_id, $employee_id, $budget_id, $budget_item, $description, $quantity, $unit_price, $discount, $sub_total);
              mysqli_stmt_execute($stmt_item);
            }
        }
    }
}

$products = $conn->query("SELECT * FROM sales_products ORDER BY name");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Create Budget</title>
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
                <h1>Add Budget</h1>
            </section>

            <section class="content">
                <?php if ($budget_added): ?>
                    <div class="alert alert-success">Budget added successfully.</div>
                <?php endif; ?>

                <!-- Add Budget Form -->
                <div class="card">
                    <div class="card-body">
                      <form method="POST">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="card">
                              <div class="card-header">
                                <h3 class="card-title">Budget Details</h3>
                              </div>
                              <div class="card-body">
                                <div class="form-group">
                                  <label>Budget Name</label>
                                  <input type="text" name="budget_name" class="form-control" required>
                                </div>

                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>Start Date</label>
                                      <input type="date" name="start_date" class="form-control" required>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="form-group">
                                      <label>End Date</label>
                                      <input type="date" name="end_date" class="form-control" required>
                                    </div>
                                  </div>
                                </div>
      
                              </div>
                            </div>
                          </div>

                          <div class="col-md-8">
                            <!-- Sales Invoice Items -->
                            <div class="card">
                              <div class="card-header">
                                <h3 class="card-title">Budget Items</h3>
                                <div class="card-tools">
                                  <button type="button" class="btn btn-sm btn-info" id="addItem">
                                    
                                    Add Item
                                  </button>
                                </div>
                              </div>
                              <div class="card-body table-responsive p-0">
                                <table class="table table-bordered" id="itemsTable">
                                  <thead>
                                    <tr>
                                      <th>Item</th>
                                      <th>Description</th>
                                      <th>Qty</th>
                                      <th>Unit Price</th>
                                      <th>Tax Rate (%)</th>
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
                                      <input type="text" name="budget_items[]" class="form-control" required>
                                    </td>
                                    <td><input type="text" name="item_description[]" class="form-control w-35" required></td>
                                    <td><input type="number" step="0.01" name="item_quantity[]" class="form-control" value="1" onchange="reCalc()"></td>
                                    <td><input type="number" step="0.01" name="item_unit_price[]" class="form-control" value="0.00" onchange="reCalc()"></td>
                                    <td><input type="number" step="0.01" name="item_tax_rate[]" class="form-control w-20" value="0.00"></td>
                                    <td><input type="number" step="0.01" name="item_sub_total[]" class="form-control w-20" value="0.00"></td>
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
                            
                            <div class="card mt-4">
                              <div class="card-body">
                                <div class="form-group">
                                  <label>Total Amount</label>
                                  <input type="number" step="0.01" name="total_amount" class="form-control" value="0.00" required>
                                </div>
                              </div>
                            </div>
                          </div>

                        </div>

                        <div class="form-group float-end mt-3">
                          <?php echo ($budget_added) ? '<a href="./" class="btn btn-danger">Close</a>' : '<a href="./" class="btn btn-danger">Cancel</a>'; ?>
                          <button type="submit" class="btn btn-primary">Add Budget</button>
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
          // let discountValue = parseFloat(row.querySelector('input[name="item_discount[]"]').value) || 0;
          // let subTotal = quantity * unitPrice * (1 - discountValue / 100);
          let subTotal = quantity * unitPrice;
          row.querySelector('input[name="item_sub_total[]"]').value = subTotal.toFixed(2);
          total += subTotal;
        });

        document.querySelector('input[name="total_amount"]').value = total.toFixed(2) || 0;
      }
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
