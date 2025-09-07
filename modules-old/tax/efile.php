<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch previous submissions
$company_id = $_SESSION['company_id'] ?? null;
$submissions = $conn->query("SELECT * FROM tax_efiling WHERE company_id = $company_id ORDER BY filed_at DESC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - E-Filing</title>
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

            <div class="content-wrapper">
              <section class="content-header">
                <h1><i class="fas fa-paper-plane"></i> E-Filing Portal</h1>
                <p>Submit tax reports to national systems & track statuses</p>
              </section>

              <section class="content">
                <div class="row">

                  <!-- Submit Filing -->
                  <div class="col-md-4">
                    <div class="card card-primary">
                      <div class="card-header"><h3 class="card-title">New Submission</h3></div>
                      <div class="card-body">
                        <form action="ajax/upload_filing.php" method="POST" enctype="multipart/form-data">
                          <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" class="form-control" required>
                          </div>
                          <div class="form-group">
                            <label>Report Period</label>
                            <input type="month" name="period" class="form-control" required>
                          </div>
                          <div class="form-group">
                            <label>Upload Report (PDF/JSON)</label>
                            <input type="file" name="report_file" class="form-control-file" accept=".pdf,.json" required>
                          </div>
                          <div class="form-group">
                            <label>Filing Method</label>
                            <select name="method" class="form-control" required>
                              <option value="api">API Upload</option>
                              <option value="manual">Manual Filing</option>
                            </select>
                          </div>
                          <button class="btn btn-success btn-block"><i class="fas fa-upload"></i> Submit Filing</button>
                        </form>
                      </div>
                    </div>
                  </div>

                  <!-- Submission Log -->
                  <div class="col-md-8">
                    <div class="card card-info">
                      <div class="card-header"><h3 class="card-title">Submission History</h3></div>
                      <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Country</th>
                              <th>Period</th>
                              <th>Method</th>
                              <th>Status</th>
                              <th>Filed At</th>
                              <th>Receipt</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($submissions as $i => $s): ?>
                            <tr>
                              <td><?= $i + 1 ?></td>
                              <td><?= htmlspecialchars($s['country']) ?></td>
                              <td><?= htmlspecialchars($s['period']) ?></td>
                              <td><?= strtoupper($s['method']) ?></td>
                              <td>
                                <span class="badge badge-<?= $s['status'] === 'success' ? 'success' : ($s['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                  <?= ucfirst($s['status']) ?>
                                </span>
                              </td>
                              <td><?= $s['filed_at'] ?></td>
                              <td>
                                <?php if ($s['receipt_path']): ?>
                                  <a href="<?= $s['receipt_path'] ?>" target="_blank" class="btn btn-sm btn-outline-info">View</a>
                                <?php else: ?>
                                  <span class="text-muted">N/A</span>
                                <?php endif ?>
                              </td>
                            </tr>
                            <?php endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                </div>
              </section>
            </div>

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
