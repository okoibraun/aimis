<?php
session_start();
require_once '../../../config/db.php';
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "view";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$id = $_GET['id'] ?? null;
$lead = $conn->query("SELECT * FROM sales_customers WHERE company_id = $company_id AND customer_type = 'lead'")->fetch_assoc();
$message = '';
if (!$lead) {
    $message = "<div class='alert alert-danger'>Lead not found.</div>";
}
$lead_id = $lead['id']; 
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Leads</title>
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
          
            <section class="content-header mt-3 mb-3">
                <h1>Lead Details: <?= $lead['title'] ?></h1>
            </section>

            <?php if(!$lead): ?>
                <?= $message; ?>
            <?php endif; ?>

            <section class="content">
                <div class="row mb-4">
                    <div class="col-3">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Lead Info</h3>
                                <div class="card-tools">
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Lead Name:</strong> <?= $lead['name'] ?></p>
                                <p><strong>Email:</strong> <?= $lead['email'] ?></p>
                                <p><strong>Phone:</strong> <?= $lead['phone'] ?></p>
                                <p><strong>Company Name:</strong> <?= $lead['company_name'] ?></p>
                                <p><strong>Position:</strong> <?= $lead['job_title'] ?></p>
                                <?php if(!empty($lead['cuty'])) { ?>
                                <p><strong>Address:</strong> <?= nl2br($lead['address']) ?></p>
                                <p><strong>City:</strong> <?= $lead['city'] ?></p>
                                <p><strong>Country:</strong> <?= $lead['country'] ?></p>
                                <p><strong>Tax ID:</strong> <?= $lead['tax_id'] ?></p>
                                <p><strong>Status:</strong> <?= $lead['is_active'] ? 'Active' : 'Inactive' ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Invoices
                                </h3>
                            </div>
                            <div class="card-body">
                              <table class="table table-hover table-striped DataTable">
                                <thead>
                                  <tr>
                                    <th>Invoice #</th>
                                    <th>Invoice Date</th>
                                    <th>Due Date</th>
                                    <th>Total (N)</th>
                                    <th>Status</th>
                                  </tr>
                                </thead>
                                <tbody>

                                  <?php $invoices = $conn->query("SELECT * FROM sales_invoices WHERE company_id = $company_id AND lead_id = $lead_id"); ?>
                                  <?php foreach ($invoices as $inv): ?>
                                  <tr>
                                    <td><?= $inv['invoice_number'] ?></td>
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
                                  </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-auto">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Actions
                                </h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <a href="#" class="btn btn-link">Contacts</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="#" class="btn btn-link">Communications</a>
                                    </li>
                                    <li class="list-group-item">
                                        
                                    </li>
                                </ul>
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

                <!-- Orders & Quotations-->
                <div class="row mb-4">

                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Quotations</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-hover table-striped DataTable">
                                <thead>
                                    <tr>
                                        <th>Quote No.</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total (N)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $quotations = $conn->query("SELECT * FROM sales_quotations WHERE company_id = $company_id AND lead_id = $lead_id"); ?>
                                    <?php foreach ($quotations as $quote): ?>
                                    <tr>
                                        <td><?= $quote['quote_number'] ?></td>
                                        <td><?= $quote['quotation_date'] ?></td>
                                        <td><span class="text text-primary"><?= $quote['status'] ?></span></td>
                                        <td>N<?= number_format($quote['total'], 2) ?></td>
                                        <td>
                                            <a href="quotation?id=<?= $quote['id'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quotations -->
                <div class="row mb-4">
                </div>

                <!-- Invoices -->
                <div class="row mb-4">
                    <div class="col">
                        <div class="card">
                            <div class="card-header"></div>
                            <div class="card-header"></div>
                        </div>
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