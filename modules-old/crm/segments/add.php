<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Set empty/default values for new segment creation
$segment = [
    'id' => '',
    'segment_name' => '',
    'target_type' => 'contacts',
    'filters' => json_encode([]),
    'company_id' => get_current_company_id()
];

$filters = []; // For new segment, empty filters
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Segments</title>
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
            <section class="content-header"><h1>Add New Segment</h1></section>
            <section class="content">
              <div class="box box-primary">
                <div class="box-body">
                  <?php include 'form.php'; ?>
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
