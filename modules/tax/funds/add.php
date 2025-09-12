<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "add";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// Fetch funds
$company_id = $_SESSION['company_id'];
$funds = $conn->prepare("SELECT * FROM tax_funds WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Funds</title>
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

            <div class="content-wrapper">
                <section class="content-header mt-3 mb-3">
                    <h1><i class="fas fa-piggy-bank"></i> Tax Funds</h1>
                </section>

                <section class="content mt-3">
                    <form action="save.php" method="POST" id="fundForm" class="card">
                        <input type="hidden" name="id" id="fund_id">
                        <div class="card-header">
                            <h5 class="card-title">New Fund Details</h5>
                            <div class="card-tools">
                                <a href="funds.php" class="btn btn-danger btn-sm">&times;</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Fund Code</label>
                                <input type="text" name="fund_code" id="fund_code" class="form-control" value="<?= "TF-" .time() ?>" readonly required>
                            </div>
                            <div class="form-group">
                                <label>Fund Name</label>
                                <input type="text" name="fund_name" id="fund_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Opening Balance</label>
                                <input type="number" name="balance" id="fund_balance" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group float-end">
                                <a href="funds.php" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-success">Save Fund</button>
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
      <?php include("../../../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../../../includes/scripts.phtml"); ?>
    <!--end::Script-->
    <script>
    // Fill modal on edit
    document.querySelectorAll('.editFundBtn').forEach(btn => {
        btn.addEventListener('click', function () {
        document.getElementById('fund_id').value = this.dataset.id;
        document.getElementById('fund_code').value = this.dataset.code;
        document.getElementById('fund_name').value = this.dataset.name;
        document.getElementById('fund_balance').value = this.dataset.balance;
        });
    });
    </script>
  </body>
  <!--end::Body-->
</html>
