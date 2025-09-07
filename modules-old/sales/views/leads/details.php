<?php
require_once '../../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Invalid Lead ID.";
    exit;
}

$lead = get_row_by_id('sales_leads', $id);
$customer = get_row_by_id('sales_customers', $lead['customer_id']);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Sales - Leads</title>
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
            <h1>Lead Details</h1>
            <a href="form.php?id=<?= $lead['id'] ?>" class="btn btn-sm btn-primary">Edit Lead</a>
            <a href="../quotations/form.php?lead_id=<?= $lead['id'] ?>" class="btn btn-sm btn-success">Convert to Quotation</a>
          </section>

          <section class="content">
            <div class="row">
              <div class="col-md-6">
                <div class="card card-info">
                  <div class="card-header">
                    <h3 class="card-title">Lead Information</h3>
                  </div>
                  <div class="card-body">
                    <p><strong>Title:</strong> <?= htmlspecialchars($lead['title']) ?></p>
                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($lead['description'])) ?></p>
                    <p><strong>Date:</strong> <?= $lead['lead_date'] ?></p>
                    <p><strong>Status:</strong> <?= $lead['status'] ?></p>
                    <p><strong>Assigned To (User ID):</strong> <?= $lead['assigned_to'] ?></p>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card card-secondary">
                  <div class="card-header">
                    <h3 class="card-title">Customer Info</h3>
                  </div>
                  <div class="card-body">
                    <p><strong>Company:</strong> <?= htmlspecialchars($customer['company_name']) ?></p>
                    <p><strong>Email:</strong> <?= $customer['email'] ?></p>
                    <p><strong>Phone:</strong> <?= $customer['phone'] ?></p>
                    <p><strong>Region:</strong> <?= $customer['region'] ?></p>
                    <p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($customer['address'])) ?></p>
                  </div>
                </div>
              </div>
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
