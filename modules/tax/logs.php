<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Fetch logs for this company
$company_id = $_SESSION['company_id'] ?? 0; // Ensure company_id is set
$logs = $conn->query("SELECT * FROM tax_audit_logs WHERE company_id = $company_id ORDER BY created_at DESC LIMIT 100");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Logs</title>
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
                <h1><i class="fas fa-clipboard-list"></i> Tax Audit & Transaction Logs</h1>
                <p>Monitor system-wide tax-impacting events across modules</p>
              </section>

              <section class="content">
                <div class="card">
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Event</th>
                          <th>Module</th>
                          <th>Entity</th>
                          <th>User</th>
                          <th>IP</th>
                          <th>Timestamp</th>
                          <th>Details</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($logs as $i => $log): ?>
                          <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($log['event_type']) ?></td>
                            <td><?= htmlspecialchars($log['module']) ?></td>
                            <td><?= htmlspecialchars($log['entity_type'] . ' #' . $log['entity_id']) ?></td>
                            <td>User #<?= $log['user_id'] ?></td>
                            <td><?= $log['ip_address'] ?></td>
                            <td><?= $log['created_at'] ?></td>
                            <td><?= nl2br(htmlspecialchars($log['details'])) ?></td>
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
