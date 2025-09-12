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

// Fetch encumbrances + funds
$encumbrances = $conn->query("
  SELECT be.*, f.fund_name, f.fund_code 
  FROM tax_budget_encumbrance be
  JOIN tax_funds f ON be.fund_id = f.id
  WHERE f.company_id = $company_id
");

// For dropdown
$fundList = $conn->query("SELECT * FROM tax_funds WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Encumbrances</title>
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
                <h1><i class="fas fa-coins"></i> Tax - Encumbrance</h1>
              </section>
              
              <section class="content">
                <?php include("../../../includes/alert.phtml"); ?>

                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas fa-coins"></i> Encumbrances
                    </h3>
                    <div class="card-tools">
                      <a href="../" class="btn btn-secondary btn-sm">Back</a>
                      <a href="add.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Encumbrance
                      </a>
                    </div>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Fund</th>
                          <th>Amount</th>
                          <th>Purpose</th>
                          <th>Encumbered Date</th>
                          <th>Released</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($encumbrances as $i => $row): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['fund_name']) ?> (<?= $row['fund_code'] ?>)</td>
                            <td>₦<?= number_format($row['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($row['purpose']) ?></td>
                            <td><?= $row['encumbered_date'] ?></td>
                            <td><?= $row['released_date'] ?: '-' ?></td>
                            <td><span class="text text-<?= $row['status'] == 'encumbered' ? 'info' : ($row['status'] == 'released' ? 'success' : 'secondary') ?>">
                              <?= ucfirst($row['status']) ?>
                            </span></td>
                            <td>
                              <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                              <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')"><i class="fas fa-trash"></i></a>
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
