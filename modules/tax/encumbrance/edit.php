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
$page = "edit";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// Fetch encumbrances + funds
$id = isset($_GET['id']) ? $_GET['id'] : null;
$encumbrance = $conn->query("SELECT * FROM tax_budget_encumbrance WHERE id = $id AND company_id = $company_id")->fetch_assoc();

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
                <h1><i class="fas fa-coins"></i> Update Encumbrance</h1>
              </section>
              
              <section class="content">
                <form action="save.php" method="POST" id="encForm" class="card">
                  <input type="hidden" name="id" id="enc_id" value="<?= $encumbrance['id'] ?>">
                  <div class="card-header">
                    <h5 class="card-title">Encumbrance Details</h5>
                    <div class="card-tools">
                        <a href="./" class="btn btn-danger btn-sm">&times;</a>
                    </div>
                  </div>

                  <div class="card-body">
                    <div class="form-group">
                      <label>Fund</label>
                      <select name="fund_id" id="enc_fund" class="form-control" required>
                        <option value="">-- Select Fund --</option>
                        <?php foreach ($fundList as $fund): ?>
                          <option value="<?= $fund['id'] ?>" <?= $encumbrance['fund_id'] == $fund['id'] ? 'selected' : '' ?>><?= htmlspecialchars($fund['fund_name']) ?> (<?= $fund['fund_code'] ?>)</option>
                        <?php endforeach ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Amount</label>
                      <input type="number" name="amount" id="enc_amount" step="0.01" class="form-control" value="<?= $encumbrance['amount'] ?>" required>
                    </div>
                    <div class="form-group">
                      <label>Purpose</label>
                      <input type="text" name="purpose" id="enc_purpose" class="form-control" value="<?= $encumbrance['purpose'] ?>" required>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                              <label>Encumbered Date</label>
                              <input type="date" name="enc_date" id="enc_date" class="form-control" value="<?= $encumbrance['encumbered_date'] ?>" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                              <label>Released Date</label>
                              <input type="date" name="rel_date" id="enc_release" class="form-control" value="<?= $encumbrance['released_date'] ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                              <label>Status</label>
                              <select name="status" id="enc_status" class="form-control">
                                <?php foreach(['encumbered', 'released', 'expired'] as $status) { ?>
                                <option value="<?= $status ?>" <?= $status == $encumbrance['status'] ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                <?php } ?>
                              </select>
                            </div>
                        </div>
                    </div>
                  </div>

                  <div class="card-footer">
                    <div class="form-group float-end">
                        <a href="./" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-success">Save Entry</button>
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
  </body>
  <!--end::Body-->
</html>
