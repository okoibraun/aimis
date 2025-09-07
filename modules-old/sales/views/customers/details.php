<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$id = $_GET['id'] ?? null;
$customer = get_row_by_id('sales_customers', $id);
$message = '';
if (!$customer) {
    $message = "<div class='alert alert-danger'>Customer not found.</div>";
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Customers</title>
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
                <h1>Customer Details: <?= htmlspecialchars($customer['company_name']) ?></h1>
            </section>

            <?php if(!$customer): ?>
                <?= $message; ?>
            <?php endif; ?>

            <section class="content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Company Info</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($customer['address'])) ?></p>
                                <p><strong>City:</strong> <?= htmlspecialchars($customer['city']) ?></p>
                                <p><strong>Country:</strong> <?= htmlspecialchars($customer['country']) ?></p>
                                <p><strong>Tax ID:</strong> <?= htmlspecialchars($customer['tax_id']) ?></p>
                                <p><strong>Status:</strong> <?= $customer['is_active'] ? 'Active' : 'Inactive' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- <div class="col-md-6">
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">Activity Summary (Coming Soon)</h3>
                            </div>
                            <div class="card-body">
                                <p>Leads, Quotations, Orders, and Invoices summary will be shown here.</p>
                            </div>
                        </div>
                    </div> -->
                </div>
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
  </body>
  <!--end::Body-->
</html>
