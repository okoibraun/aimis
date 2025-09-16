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

// Fetch previously generated reports
$reports = $conn->query("
SELECT tr.*, u.name AS user_name
FROM tax_reports tr
JOIN users u ON u.id = tr.generated_by
WHERE tr.company_id = $company_id AND u.company_id = tr.company_id
ORDER BY generated_at DESC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Reports</title>
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
                <h1><i class="fas fa-file-alt"></i> Tax & IPSAS Reports</h1>
                <p>
                  Generate tax filing and IPSAS-compliant reports
                  <a href="../" class="btn btn-secondary btn-sm float-end">Back</a>
                </p>
              </section>

              <section class="content">
                <?php include("../../../includes/alert.phtml"); ?>
                
                <div class="row">

                  <!-- Generate New Report -->
                  <div class="col-3">
                    <div class="card card-primary">
                      <div class="card-header"><h3 class="card-title">Generate New Report</h3></div>
                      <div class="card-body">
                        <form action="generate.php" method="POST" class="card">
                        <div class="card-body">

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
                            <div class="row">
                              <div class="col">
                                <div class="form-group">
                                  <label for="from">From:</label>
                                  <input type="date" name="from" class="form-control" required>
                                </div>
                              </div>
                              <div class="col">
                                <label for="to">To:</label>
                                <input type="date" name="to" class="form-control" required>
                              </div>
                            </div>
                        </div>
                          <div class="card-footer">
                            <div class="form-group float-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-cogs"></i> Generate Report
                                </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                  <!-- Existing Reports -->
                  <div class="col">
                    <div class="card card-info">
                      <div class="card-header"><h3 class="card-title">Generated Reports</h3>
                      <div class="card-tools tableToolbar"></div>
                    </div>
                      <div class="card-body table-responsive">
                        <table class="table table-bordered table-striped DataTable">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Type</th>
                              <th>Range</th>
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
                                <td><?= $r['date_from'] ?> - <?= $r['date_to'] ?></td>
                                <td><?= $r['generated_at'] ?></td>
                                <td>User: <?= $r['user_name'] ?></td>
                                <td>
                                  <form action="report.php" method="post">
                                    <input type="hidden" name="report_type" value="<?= $r['report_type'] ?>">
                                    <input type="hidden" name="date_from" value="<?= $r['date_from'] ?>">
                                    <input type="hidden" name="date_to" value="<?= $r['date_to'] ?>">
                                    <button type="submit" class="btn btn-sm btn-info">View</button>
                                  </form>
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
