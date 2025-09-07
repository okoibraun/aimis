<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
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
                        <td><?= htmlspecialchars($a['subject']) ?></td>
                        <td><?= ucfirst($a['related_type']) ?> #<?= $a['related_id'] ?></td>
                        <td><?= htmlspecialchars($a['due_date']) ?></td>
                        <td><?= ucfirst($a['status']) ?></td>
                        <td><?php $uname = $conn->query("SELECT name FROM users WHERE id={$a['assigned_to']}")->fetch_assoc(); echo $uname['name']; ?></td>
                        <td>
                          <a href="view.php?id=<?= $a['id'] ?>" class="btn btn-xs btn-info">View</a>
                          <a href="edit.php?id=<?= $a['id'] ?>" class="btn btn-xs btn-warning">Edit</a>
                          <a href="delete.php?id=<?= $a['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this activity?')">Delete</a>
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
