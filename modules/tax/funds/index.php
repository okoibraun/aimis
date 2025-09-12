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
$page = "list";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// Fetch funds
$company_id = $_SESSION['company_id'];
$funds = $conn->query("SELECT * FROM tax_funds WHERE company_id = $company_id");
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

                <section class="content">
                    <?php include("../../../includes/alert.phtml"); ?>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-piggy-bank"></i> Funds
                            </h3>
                            <div class="card-tools">
                                <a href="../" class="btn btn-secondary btn-sm">Back</a>
                                <a href="add.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Fund
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped DataTable">
                            <thead>
                                <tr>
                                <th>#</th>
                                <th>Fund Code</th>
                                <th>Name</th>
                                <th>Balance</th>
                                <th>Created</th>
                                <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($funds as $i => $fund): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= $fund['fund_code'] ?></td>
                                    <td><?= $fund['fund_name'] ?></td>
                                    <td>₦<?= number_format($fund['balance'], 2) ?></td>
                                    <td><?= $fund['created_at'] ?></td>
                                    <td>
                                    <a href="edit.php?id=<?= $fund['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    <a href="delete.php?id=<?= $fund['id'] ?>&fund_code<?= $fund['fund_code'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this fund?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach ?>
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
