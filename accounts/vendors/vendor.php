<?php
require_once '../../modules/sales/includes/helpers.php';
include("../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
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
$vendor = $conn->query("SELECT * FROM accounts_vendors WHERE id = $id AND company_id = $company_id LIMIT 1")->fetch_assoc();
$message = '';
if (!$vendor) {
    $message = "<div class='alert alert-danger'>Vendor not found.</div>";
}
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Accounts - Vendors</title>
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
                <h3>Vendor Details: <?= htmlspecialchars($vendor['name']) ?></h3>
            </section>

            <?php if(!$vendor): ?>
                <?= $message; ?>
            <?php endif; ?>

            <section class="content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Company </h3>
                                <div class="card-tools">
                                    <a href="./" class="btn btn-secondary btn-sm">Back</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Email:</strong> <?= htmlspecialchars($vendor['email']) ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($vendor['phone']) ?></p>
                                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($vendor['address'])) ?></p>
                                <p><strong>City:</strong> <?= htmlspecialchars($vendor['city']) ?></p>
                                <p><strong>Country:</strong> <?= htmlspecialchars($vendor['country']) ?></p>
                                <p><strong>Tax ID:</strong> <?= htmlspecialchars($vendor['tax_id']) ?></p>
                                <p><strong>Status:</strong> <?= $vendor['is_active'] ? 'Active' : 'Inactive' ?></p>
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
      <?php include("../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
