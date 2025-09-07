<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/helpers.php';
require_once '../../functions/log_functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('../../login.php');
}

$is_superadmin = $_SESSION['role'] === 'superadmin';
$company_id = $is_superadmin && isset($_GET['company_id']) 
    ? intval($_GET['company_id']) 
    : $_SESSION['company_id'];

$logs = get_activity_logs_by_company($company_id);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Audit Logs</title>
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
              <section class="content-header mt-3 mb-5">
                <h1>Activity Logs</h1>
              </section>

              <section class="content">
                <div class="container-fluid">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>User Email</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP</th>
                        <th>Agent</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($logs as $log): ?>
                        <tr>
                          <td><?= htmlspecialchars($log['email']) ?></td>
                          <td><?= htmlspecialchars($log['action']) ?></td>
                          <td><?= htmlspecialchars($log['description']) ?></td>
                          <td><?= $log['ip_address'] ?></td>
                          <td><?= substr($log['user_agent'], 0, 30) ?>...</td>
                          <td><?= $log['created_at'] ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
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
