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

// Fetch grants
$id = isset($_GET['id']) ? $_GET['id'] : '';
$grant = $conn->query("SELECT * FROM tax_grants WHERE id = $id AND company_id = $company_id")->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Grants</title>
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
                <h1>Tax - Grant & Aid Tracking</h1>
              </section>

              <section class="content">
                <form action="save.php" method="POST" id="grantForm" class="card">
                  <input type="hidden" name="id" id="grant_id" value="<?= $grant['id'] ?>">
                    <div class="card-header">
                      <h5 class="card-title">New Grant Details</h5>
                      <div class="card-tools">
                        <a href="./" class="btn btn-danger btn-sm">&times;</a>
                      </div>
                    </div>

                    <div class="card-body">
                      <div class="form-group">
                        <label>Grant Name</label>
                        <input type="text" name="grant_name" id="grant_name" class="form-control" value="<?= $grant['grant_name'] ?>" required>
                      </div>
                      <div class="form-group">
                        <label>Source</label>
                        <input type="text" name="source" id="grant_source" class="form-control" value="<?= $grant['source'] ?>">
                      </div>
                      <div class="row">
                        <div class="col">
                            <div class="form-group">
                              <label>Amount Awarded</label>
                              <input type="number" step="0.01" name="amount_awarded" id="grant_awarded" class="form-control" value="<?= $grant['amount_awarded'] ?>" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                              <label>Amount Spent</label>
                              <input type="number" step="0.01" name="amount_spent" id="grant_spent" class="form-control" value="<?= $grant['amount_spent'] ?>">
                            </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                            <div class="form-group">
                              <label>Start Date</label>
                              <input type="date" name="start_date" id="grant_start" class="form-control" value="<?= $grant['start_date'] ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                              <label>End Date</label>
                              <input type="date" name="end_date" id="grant_end" class="form-control" value="<?= $grant['end_date'] ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                              <label>Status</label>
                              <select name="status" id="grant_status" class="form-control">
                                <?php foreach(['active', 'completed', 'cancelled'] as $status) { ?>
                                <option value="<?= $status ?>" <?= $grant['status'] == $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                <?php } ?>
                              </select>
                            </div>
                        </div>
                      </div>
                    </div>

                    <div class="card-footer">
                        <div class="form-group float-end">
                            <a href="./" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-success">Update Grant</button>
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
