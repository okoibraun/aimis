<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch previously generated reports
$company_id = $_SESSION['company_id'] ?? 0; // Ensure company_id is set
$reports = $conn->query("SELECT * FROM tax_reports WHERE company_id = $company_id ORDER BY generated_at DESC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Reports</title>
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
                <h1><i class="fas fa-file-alt"></i> Tax & IPSAS Reports</h1>
                <p>
                  Generate tax filing and IPSAS-compliant reports
                  <a href="index.php" class="btn btn-secondary btn-sm float-end">Back</a>
                </p>
              </section>

              <section class="content">
                <div class="row">

                  <!-- Generate New Report -->
                  <div class="col-md-4">
                    <div class="card card-primary">
                      <div class="card-header"><h3 class="card-title">Generate New Report</h3></div>
                      <div class="card-body">
                        <form action="ajax/generate_report.php" method="POST">
                          <div class="form-group">
                            <label for="report_type">Report Type</label>
                            <select name="report_type" id="report_type" class="form-control" required>
                              <option value="">-- Select --</option>
                              <option value="VAT">VAT Filing Report</option>
                              <option value="WHT">Withholding Tax Report</option>
                              <option value="Annual">Annual Tax Filing</option>
                              <option value="IPSAS">IPSAS Compliance Report</option>
                            </select>
                          </div>
                          <button type="submit" class="btn btn-success"><i class="fas fa-cogs"></i> Generate Report</button>
                        </form>
                      </div>
                    </div>
                  </div>

                  <!-- Existing Reports -->
                  <div class="col-md-8">
                    <div class="card card-info">
                      <div class="card-header"><h3 class="card-title">Generated Reports</h3></div>
                      <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Type</th>
                              <th>Generated At</th>
                              <th>By</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($reports as $i => $r): ?>
                              <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= $r['report_type'] ?></td>
                                <td><?= $r['generated_at'] ?></td>
                                <td>User #<?= $r['generated_by'] ?></td>
                                <td>
                                  <a href="ajax/view_report.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info" target="_blank">
                                    View
                                  </a>
                                  <a href="ajax/download_report.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-secondary">
                                    Download
                                  </a>
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
