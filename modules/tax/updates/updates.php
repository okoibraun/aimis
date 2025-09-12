<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Fetch auto-update history
$company_id = $_SESSION['company_id'] ?? null;
$updates = $conn->query("SELECT * FROM tax_rate_updates WHERE company_id = $company_id ORDER BY updated_at DESC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Updates</title>
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
              <section class="content-header mt-3 mb-5">
                <h1><i class="fas fa-sync-alt"></i> Regional Tax Rate Auto-Updates</h1>
                <p>
                    View or trigger country-specific tax rate sync and updates
                    <a href="index.php" class="btn btn-secondary btn-sm float-end">Back</a>
                </p>
              </section>

              <section class="content">
                <div class="card">
                  <div class="card-header">
                    <form class="form-inline" action="ajax/sync_rates.php" method="POST">
                      <div class="form-group">
                        <label class="mr-2">Sync Country:</label>
                        <input type="text" name="country" class="form-control mr-2" placeholder="e.g., Nigeria, Germany" required>
                        <button class="btn btn-success"><i class="fas fa-cloud-download-alt"></i> Sync Now</button>
                      </div>
                    </form>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Country</th>
                          <th>Source</th>
                          <th>Rates Synced</th>
                          <th>Status</th>
                          <th>Updated At</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($updates as $i => $u): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $u['country'] ?></td>
                            <td><?= $u['source'] ?></td>
                            <td><?= $u['rate_details'] ?></td>
                            <td>
                              <span class="badge badge-<?= $u['status'] === 'success' ? 'success' : 'danger' ?>">
                                <?= ucfirst($u['status']) ?>
                              </span>
                            </td>
                            <td><?= $u['updated_at'] ?></td>
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
