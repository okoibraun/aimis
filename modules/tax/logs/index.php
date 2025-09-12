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
$page = "audit";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

// Fetch logs for this company
$logs = $conn->query("
SELECT tal.*, u.name AS user_name
FROM tax_audit_logs tal
JOIN users u ON u.id = tal.user_id
WHERE tal.company_id = $company_id AND u.company_id = tal.company_id
ORDER BY created_at DESC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Tax - Logs</title>
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
              <section class="content-header mt-4 mb-4">
                <h1><i class="fas fa-clipboard-list"></i> Tax Logs</h1>
                <p>Monitor system-wide tax-impacting events across modules</p>
              </section>

              <section class="content">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Logs</h3>
                    <div class="card-tools">
                      <a href="../" class="btn btn-secondary btn-sm">Back</a>
                    </div>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover DataTable">
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
                            <td><?= $log['event_type'] ?></td>
                            <td><?= $log['module'] ?></td>
                            <td><?= $log['entity_type'] . ' #' . $log['entity_id'] ?></td>
                            <td><?= $log['user_name'] ?></td>
                            <td><?= $log['ip_address'] ?></td>
                            <td><?= $log['created_at'] ?></td>
                            <td><?= nl2br($log['details']) ?></td>
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
