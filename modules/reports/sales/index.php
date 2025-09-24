<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Check User Permissions
// $page = "list";
// $user_permissions = get_user_permissions($_SESSION['user_id']);

// if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
//     die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
//     exit;
// }

$status = $_GET['status'] ?? '';
$customer_id = $_GET['customer_id'] ?? '';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Fetch customers (students)
$customers = $conn->query("SELECT id, name FROM sales_customers WHERE company_id = $company_id ORDER BY name");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales Report</title>
    <?php include_once("../../../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../../../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../../../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

          <section class="content-header mt-3 mb-5">
            <h1>Sales Report</h1>
          </section>

          <section class="content">

            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Reports</h4>
                <div class="card-tools">
                    <form method="get" class="row mb-2">
                        <div class="col-auto">
                            <select name="customer_id" class="form-control mx-2">
                                <option value="">All Customers</option>
                                <?php foreach($customers as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $customer_id == $c['id'] ? 'selected' : '' ?>>
                                    <?= $c['name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-auto">
                        <select name="status" class="form-control mx-2">
                            <option value="">All Status</option>
                            <option value="paid" <?= $status == 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="unpaid" <?= $status == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                            <option value="partial" <?= $status == 'partial' ? 'selected' : '' ?>>Partial</option>
                        </select>
                        </div>
                        <div class="col-auto">From: </div>
                        <div class="col-auto">
                        <input type="date" name="start_date" value="<?= $start_date ?>" placeholder="From: " class="form-control mx-2">
                        </div>
                        <div class="col-auto">To: </div>
                        <div class="col-auto">
                        <input type="date" name="end_date" value="<?= $end_date ?>" placeholder="To: " class="form-control mx-2">
                        </div>
                        <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
              </div>
              <div class="card-body table-responsive">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        // $sql = "
                        //   SELECT i.*, c.name,
                        //         (SELECT SUM(amount) FROM payables WHERE invoice_number = i.id) AS paid
                        //   FROM invoices i
                        //   JOIN sales_customers c ON c.id = i.customer_id
                        //   WHERE i.due_date BETWEEN '$start_date' AND '$end_date'
                        // ";
                        
                        $sql = "
                        SELECT i.*, 
                                (SELECT SUM(amount) FROM sales_invoice_payments WHERE invoice_id = i.id) AS amount_paid
                        FROM sales_invoices i
                        WHERE i.company_id = $company_id AND due_date >= '$start_date' AND due_date <= '$end_date'
                        ";

                        if ($customer_id) $sql .= " AND i.customer_id = $customer_id";

                        $result = mysqli_query($conn, $sql);
                        foreach($result as $row) {
                          $paid = $row['amount_paid'] ?? 0;
                          $balance = $row['total_amount'] - $paid;

                          if ($status == 'paid' && $balance > 0) continue;
                          if ($status == 'unpaid' && $paid > 0) continue;
                          if ($status == 'partial' && ($paid == 0 || $balance == 0)) continue;

                          $status_label = ($balance <= 0) ? 'Paid' : (($paid > 0) ? 'Partial' : 'Unpaid');
                      ?>
                      <tr>
                        <td><?= $row['invoice_number'] ?></td>
                        <td>
                          <?php
                          $customer;
                          if($row['customer_id']) {
                            $customer = $conn->query("SELECT * FROM sales_customers WHERE id = {$row['customer_id']}")->fetch_assoc();
                            echo ($customer && $customer['customer_type'] == "customer") ? $customer['name'] : "{$customer['name']} (Lead)";
                          }
                          ?>
                        </td>
                        <td><?= $row['invoice_date'] ?></td>
                        <td><?= $row['due_date'] ?></td>
                        <td><?= number_format($row['total_amount'], 2) ?></td>
                        <td><?= number_format($paid, 2) ?></td>
                        <td><?= number_format($balance, 2) ?></td>
                        <td class="text text-<?= 
                        match($status_label) {
                          'Paid' => 'success',
                          'Partial' => 'primary',
                          'Unpaid' => 'danger',
                          default => 'info'
                        }
                        ?>"><?= $status_label ?></td>
                        <td>
                          <a href="invoice?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
              </div>
            </div>
          </section>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
