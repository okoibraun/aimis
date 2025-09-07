<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/subscription_functions.php';
require_once '../../functions/helpers.php';

if (!isset($_SESSION['user_id'])) {
    redirect('../../login.php');
}

$company_id = $_SESSION['company_id'];
$history = get_subscription_history($company_id);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Subscriptions</title>
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
                <h1>Subscription History</h1>
              </section>

              <section class="content">
                <div class="card">
                  <div class="card-body">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Plan</th>
                          <th>Status</th>
                          <th>Start</th>
                          <th>End</th>
                          <th>Date Created</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($history as $entry): ?>
                          <tr>
                            <td><?= htmlspecialchars($entry['plan_name']) ?></td>
                            <td><?= $entry['status'] ?></td>
                            <td><?= $entry['start_date'] ?></td>
                            <td><?= $entry['end_date'] ?></td>
                            <td><?= $entry['created_at'] ?></td>
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
