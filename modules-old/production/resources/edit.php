<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$id = $_GET['id'];
$res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM production_resources WHERE id = $id"));
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
                <section class="content-header"><h1>Edit Resource</h1></section>
                <section class="content">
                    <form action="save.php" method="post">
                        <input type="hidden" name="id" value="<?= $res['id'] ?>">
                        <div class="form-group">
                            <label>Resource Name</label>
                            <input type="text" name="name" class="form-control" value="<?= $res['name'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" name="code" class="form-control" value="<?= $res['code'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control" required>
                                <option value="Manpower" <?= $res['type'] === 'Manpower' ? 'selected' : '' ?>>Manpower</option>
                                <option value="Machine" <?= $res['type'] === 'Machine' ? 'selected' : '' ?>>Machine</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="Available" <?= $res['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                                <option value="In Use" <?= $res['status'] === 'In Use' ? 'selected' : '' ?>>In Use</option>
                                <option value="Maintenance" <?= $res['status'] === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            </select>
                        </div>
                        <button type="submit" name="action" value="update" class="btn btn-success">Update</button>
                    </form>
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
