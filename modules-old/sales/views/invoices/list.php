<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$invoices = $db->query("
    SELECT i.*, c.company_id, o.order_number
    FROM sales_invoices i
    LEFT JOIN sales_customers c ON i.customer_id = c.id
    LEFT JOIN sales_orders o ON i.order_id = o.id
    ORDER BY i.invoice_date DESC
");
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
            <!-- <section class="content-header">
              <h1>Invoices</h1>
              <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Invoices</li>
              </ol>
            </section> -->

            <section class="content mt-5">
              <div class="card">
                <div class="card-header with-border">
                    <div class="row">
                        <div class="col-lg-6">
                            <h4>Invoice List</h4>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="invoices.php?action=create" class="btn btn-primary">
                              <i class="fa fa-plus"></i> New Invoice
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                  <table class="table table-hover table-striped">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Order #</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($invoices as $inv): ?>
                      <tr>
                        <td><?= $inv['id'] ?></td>
                        <td><?= htmlspecialchars($inv['invoice_number']) ?></td>
                        <td><?= htmlspecialchars($inv['company_name']) ?></td>
                        <td><?= htmlspecialchars($inv['order_number']) ?></td>
                        <td><?= $inv['invoice_date'] ?></td>
                        <td><?= $inv['due_date'] ?></td>
                        <td>$<?= number_format($inv['total_amount'], 2) ?></td>
                        <td>
                          <span class="label label-<?= match($inv['status']) {
                            'unpaid' => 'danger',
                            'partial' => 'warning',
                            'paid' => 'success',
                            'overdue' => 'default',
                            default => 'info'
                          } ?>"><?= ucfirst($inv['status']) ?></span>
                        </td>
                        <td>
                          <a href="invoices.php?action=details&id=<?= $invoice['id'] ?>" class="btn btn-xs btn-info">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="invoices.php?action=edit&id=<?= $invoice['id'] ?>" class="btn btn-xs btn-warning">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a href="invoices.php?action=delete&id=<?= $invoice['id'] ?>" 
                            class="btn btn-xs btn-danger" 
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
