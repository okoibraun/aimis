<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}


$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);
$memo_id = (isset($_GET['memo_id']) ? intval($_GET['memo_id']) : 0);
if ($id == 0) {
    header('Location: ./');
    exit();
}

// Fetch memo details
$query = "SELECT * FROM memo_versions WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $query);
$memo = mysqli_fetch_assoc($result);

if (!$memo) {
    echo "Memo version not found.";
    //exit();
}

?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Memo Version</title>
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
                        <div class="row mb-1 mt-4">
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
                <!-- /.card -->
                </div>
                <!-- /.Start col -->
            </div>

            <div class="row">
                <!-- col-lg-8 -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><strong><?php echo htmlspecialchars($memo['title']); ?></strong></h3>
                        </div>
                        <div class="card-body">
                            <div class="content">
                                <?php echo $memo['content']; ?>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Created By:</strong> 
                                    <?php
                                        $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM users WHERE id={$memo['edited_by']}"))['name'];
                                        echo $user;
                                    ?>
                                </div>
                                <div class="col-sm-6 text-end">
                                    <strong>Edited At:</strong> <?php echo $memo['edited_at']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-lg-8 -->

                 <!-- col-lg-4 -->
                <div class="col-lg-4">
                    <div class="row">
                        
                    </div>
                </div>
                <!-- / col-lg-4 -->
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
