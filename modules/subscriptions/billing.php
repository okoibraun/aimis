<?php
session_start();
require_once '../../config/db.php';
require_once '../../functions/subscription_functions.php';
require_once '../../functions/helpers.php';

if (!isset($_SESSION['user_id'])) {
    redirect('../../login.php');
}

$company_id = $_SESSION['company_id'];
$current = get_company_subscription($company_id);
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
                <h1>My Subscription</h1>
              </section>

              <section class="content">
                <div class="card">
                  <div class="card-body">
                    <?php if ($current): ?>
                      <p><strong>Plan:</strong> <?= htmlspecialchars($current['plan_name']) ?></p>
                      <p><strong>Status:</strong> <?= $current['status'] ?></p>
                      <p><strong>Start Date:</strong> <?= $current['start_date'] ?></p>
                      <p><strong>End Date:</strong> <?= $current['end_date'] ?></p>
                    <?php else: ?>
                      <p>No active subscription found.</p>
                    <?php endif ?>
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
