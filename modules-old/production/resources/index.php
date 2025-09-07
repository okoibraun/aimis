<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$result = $conn->query("SELECT * FROM production_resources ORDER BY name ASC");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | Production - Resources</title>
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
                <section class="content-header">
                    <h1>Production Resources</h1>
                    <a href="create.php" class="btn btn-primary">Add Resource</a>
                </section>
                <section class="content">
                    <table class="table table-bordered">
                        <thead><tr><th>Name</th><th>Type</th><th>Code</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach($result as $r): ?>
                            <tr>
                                <td><?= $r['name'] ?></td>
                                <td><?= $r['type'] ?></td>
                                <td><?= $r['code'] ?></td>
                                <td><?= $r['status'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete.php?id=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this resource?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
