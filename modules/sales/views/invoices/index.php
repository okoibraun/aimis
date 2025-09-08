<?php
require_once '../../includes/helpers.php';
include("../../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

if(in_array($_SESSION['user_role'], system_users())) {
  $invoices = $db->query("
      SELECT i.*, c.name, l.title, q.quote_number, o.order_number
      FROM sales_invoices i
      LEFT JOIN sales_customers l ON i.lead_id = l.id
      LEFT JOIN sales_quotations q ON i.quotation_id = q.id
      LEFT JOIN sales_customers c ON i.customer_id = c.id
      LEFT JOIN sales_orders o ON i.order_id = o.id
      ORDER BY i.invoice_date DESC
  ");
} else {
  $invoices = $db->query("
      SELECT i.*, c.name, l.title, q.quote_number, o.order_number
      FROM sales_invoices i
      LEFT JOIN sales_customers l ON i.lead_id = l.id
      LEFT JOIN sales_quotations q ON i.quotation_id = q.id
      LEFT JOIN sales_customers c ON i.customer_id = c.id
      LEFT JOIN sales_orders o ON i.order_id = o.id
      WHERE i.company_id = $company_id
      ORDER BY i.invoice_date DESC
  ");
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
              <h1>Manage Invoices</h1>
            </section>

            <section class="content">
              <div class="card">
                <div class="card-header with-border">
                    <div class="row">
                        <div class="col-lg-6">
                            <h4>Invoices</h4>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="add" class="btn btn-primary">
                              <i class="fa fa-plus"></i> New Invoice
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                  <table class="table table-hover table-striped DataTable">
                    <thead>
                      <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Lead Title</th>
                        <th>Quotation #</th>
                        <th>Order #</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total (N)</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($invoices as $inv): ?>
                      <tr>
                        <td><?= $inv['invoice_number'] ?></td>
                        <td><?= $inv['name'] ?></td>
                        <td><?= $inv['title']; ?></td>
                        <td><?= $inv['quote_number']; ?></td>
                        <td><?= $inv['order_number'] ?></td>
                        <td><?= $inv['invoice_date'] ?></td>
                        <td><?= $inv['due_date'] ?></td>
                        <td>N<?= number_format($inv['total_amount'], 2) ?></td>
                        <td>
                          <span class="text text-<?= match($inv['status']) {
                            'unpaid' => 'danger',
                            'partial' => 'warning',
                            'paid' => 'success',
                            'overdue' => 'default',
                            default => 'info'
                          } ?>"><?= ucfirst($inv['status']) ?></span>
                        </td>
                        <td>
                          <a href="invoice.php?id=<?= $inv['id'] ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="edit.php?id=<?= $inv['id'] ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a href="invoices?action=delete&id=<?= $inv['id'] ?>" 
                            class="btn btn-sm btn-danger" 
                            onclick="return confirm('Are you sure you want to delete this invoice?');">
                            <i class="fas fa-trash"></i>
                          </a>

                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
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
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
