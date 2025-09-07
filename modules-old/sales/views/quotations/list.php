<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$quotations = get_all_rows('sales_quotations', 'quote_date DESC');
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
            

          <section class="content mt-5">
            <div class="card card-default">
                <div class="card-header">
                  <div class="row">
                    <div class="col-md-6">
                      <h4>Quotations</h4>
                    </div>
                    <div class="col-md-6 text-end">
                      <a href="add_quotation.php" class="btn btn-primary">Create Quotation</a>
                    </div>
                  </div>
                </div>
              <div class="card-body table-responsive">
                <table class="table table-hover table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Quote No.</th>
                      <th>Customer</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Total</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($quotations as $quote): ?>
                      <?php $customer = get_row_by_id('sales_customers', $quote['customer_id']); ?>
                      <tr>
                        <td><?= $quote['id'] ?></td>
                        <td><?= $quote['quote_number'] ?></td>
                        <td><?= htmlspecialchars($customer['company_name']) ?></td>
                        <td><?= $quote['quote_date'] ?></td>
                        <td><span class="badge badge-info"><?= $quote['status'] ?></span></td>
                        <td>$<?= number_format($quote['total_amount'], 2) ?></td>
                        <td>
                          <a href="edit.php?id=<?= $quote['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                          <a href="form.php?action=delete&id=<?= $quote['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this quotation?')">Delete</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

          <?php include("_modal.php"); ?>
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
