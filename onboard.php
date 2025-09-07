<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS Cloud | Onboard</title>
    <?php include_once("includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
      <div class="app-content">
        <div class="container-fluid">

            <div class="container">
                <section class="content-header mt-5"></section>
                <section class="content">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0">Get Started</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Welcome Onboard <strong><?= $_SESSION['user_name']; ?></strong></h6>
                            <br><br>
                            <p class="card-text">We are glad to have you join the largest ERP solution in Nigeria. Now that you are here, start by adding your Company/Organization</p>
                            <a href="/modules/company/create.php" class="btn btn-primary">Add New</a>
                        </div>
                    </div>
                </section>
            </div>

        </div>
      </div>
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
