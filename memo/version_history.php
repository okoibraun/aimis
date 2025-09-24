<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$memo_id = $_GET['memo_id'];
$version_query = "SELECT * FROM memo_versions WHERE memo_id = $memo_id ORDER BY edited_at DESC";
// Fetch version history
$versions = mysqli_query($conn, $version_query);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memos Dashboard</title>
    <?php include_once("../includes/head.phtml"); ?>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <?php include_once("../includes/header.phtml"); ?>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <?php include_once("../includes/sidebar.phtml"); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <!-- Start col -->
                <div class="col-lg-12 connectedSortable">
                    <!-- begin filter section -->
                    <!-- / end filter section -->

                    <!--begin::Container-->
                    <div class="container-fluid">
                        <!--begin::Row-->
                        <div class="row mt-4 mb-1">
                            <div class="col-lg-12 col-sm-6">
                                <div class="float-sm-end">
                                    <a href="memo?id=<?= $memo_id; ?>" class="btn btn-secondary">Back to Memo</a>
                                </div>
                            </div>
                        </div>
                        <!--end::Row-->
                    </div>
                    <hr>
                    <div class="container-fluid">
                        <!--begin::Row-->
                        <div class="row mt-4 mb-4">
                            <div class="col-sm-6 col-lg-12">
                                <h2 class="mb-0">Version History</h2>
                            </div>
                        </div>
                        <!--end::Row-->
                    </div>
                    <!--end::Container-->
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Memo Title</th>
                                <th>User</th>
                                <th>Date / Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($versions as $version) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($version['title']); ?></td>
                                    <td>
                                        <?php
                                            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM users WHERE id={$version['edited_by']}"))['name'];
                                            echo $user;
                                        ?>
                                    </td>
                                    <td><?= $version['edited_at']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <!-- /.card -->
                </div>
                <!-- /.Start col -->
            </div>
            <!-- /.row (main row) -->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <?php include("../includes/footer.phtml"); ?>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <?php include("../includes/scripts.phtml"); ?>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
