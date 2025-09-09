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
$page = "view";
$user_permissions = get_user_permissions($_SESSION['user_id']);

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$company_id = get_current_company_id();

$stmt = $conn->prepare("SELECT * FROM crm_activities WHERE company_id = ? ORDER BY due_date DESC");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Activities</title>
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
                  <h2 class="">CRM Activities</h2>
                </div>
                <div class="col-lg-6 text-end">
                  <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="../../../index.php"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="#">CRM</a></li>
                    <li class="breadcrumb-item active">Activities</li>
                  </ol>
                </div>
              </div>
            </section>

            <section class="content">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-lg-6">
                      <h3 class="card-title">Activities List</h3>
                    </div>
                    <div class="col-lg-6 text-end">
                      <a href="add.php" class="btn btn-primary btn-sm">+ Add Activity</a>
                    </div>
                  </div>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-striped" id="activityTable">
                    <thead>
                      <tr>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Related To</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($activities as $a): ?>
                      <tr>
                        <td><?= ucfirst($a['type']) ?></td>
                        <td><?= $a['subject'] ?></td>
                        <?php $related_to = $conn->query("SELECT name, title, customer_type FROM sales_customers WHERE id={$a['related_id']}")->fetch_assoc(); ?>
                        <td><?= ucfirst($a['related_type']) ?> : <?= !empty($a['related_id']) ? ($related_to['customer_type'] == 'customer' ? $related_to['name'] : $related_to['title']) : 'none'; ?></td>
                        <td><?= $a['due_date'] ?></td>
                        <td><?= ucfirst($a['status']) ?></td>
                        <td><?= $uname = !empty($a['assigned_to']) ? $conn->query("SELECT name FROM users WHERE id={$a['assigned_to']}")->fetch_assoc()['name'] : 'none'; ?></td>
                        <td>
                          <a href="edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="delete.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this activity?')">
                            <i class="fas fa-trash"></i>
                          </a>
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
