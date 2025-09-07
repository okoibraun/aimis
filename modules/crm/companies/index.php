<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';
include("../../../functions/role_functions.php");

//Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Check User Permissions
$page = "list";
$user_permissions = get_user_permissions($_SESSION['user_id']);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM crm_companies WHERE company_id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$companies = $result->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Companies</title>
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
              <div class="row">
                <div class="col-lg-6">
                  <h2 class="">CRM Companies</h2>
                </div>
                <div class="col-lg-6 text-end">
                  <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="/index.php"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="#">CRM</a></li>
                    <li class="breadcrumb-item active">Companies</li>
                  </ol>
                </div>
              </div>
            </section>

            <section class="content">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-lg-6">
                      <h3 class="card-title">Companies</h3>
                    </div>
                    <div class="col-lg-6 text-end">
                      <a href="add.php" class="btn btn-primary btn-sm">+ Add Company</a>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <table class="table table-bordered table-striped" id="companiesTable">
                    <thead>
                      <tr>
                        <th>Company Name</th>
                        <th>Industry</th>
                        <th>Phone</th>
                        <th>Website</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($companies as $co): ?>
                      <tr>
                        <td><?= htmlspecialchars($co['name']) ?></td>
                        <td><?= htmlspecialchars($co['industry']) ?></td>
                        <td><?= htmlspecialchars($co['phone']) ?></td>
                        <td><a href="https://<?= htmlspecialchars($co['website']) ?>" target="_blank"><?= htmlspecialchars($co['website']) ?></a></td>
                        <td>
                          <a href="view.php?id=<?= $co['id'] ?>" class="btn btn-xs btn-info">View</a>
                          <a href="edit.php?id=<?= $co['id'] ?>" class="btn btn-xs btn-warning">Edit</a>
                          <a href="delete.php?id=<?= $co['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this company?')">Delete</a>
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
