<?php
session_start();
// Include database connection and header
// This file should be included at the top of your PHP files to establish a database connection and include common header elements.
include('../../../config/db.php');
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($user_id)) {
    header('Location: /login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$result = $conn->query("SELECT * FROM production_resources WHERE company_id = $company_id ORDER BY name ASC");
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
                <section class="content-header mt-3 mb-3">
                    <h1>Production Resources</h1>
                    
                </section>

                <section class="content">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Resources</h3>
                      <div class="card-tools">
                        <a href="create.php" class="btn btn-primary">Add Resource</a>
                      </div>
                    </div>
                    <div class="card-body table-responsive">
                      <table class="table table-bordered DataTable">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>Type</th>
                              <th>Code</th>
                              <th>Status</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                              <?php foreach($result as $r): ?>
                              <tr>
                                  <td><?= $r['name'] ?></td>
                                  <td><?= $r['type'] ?></td>
                                  <td><?= $r['code'] ?></td>
                                  <td><?= $r['status'] ?></td>
                                  <td>
                                      <a href="edit.php?id=<?= $r['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                      <a href="delete.php?id=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this resource?')"><i class="fas fa-trash"></i></a>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
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
