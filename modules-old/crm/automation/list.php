<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$company_id = get_current_company_id();
$result = $conn->query("SELECT * FROM crm_automation_rules WHERE company_id = $company_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Automations</title>
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
                  <h2 class="">CRM Automations</h2>
                </div>
                <div class="col-lg-6 text-end">
                  <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="../../../index.php"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="#">CRM</a></li>
                    <li class="breadcrumb-item active">Automations</li>
                  </ol>
                </div>
              </div>
            </section>

            <section class="content">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-lg-6">
                      <h3 class="card-title">Automation Rules</h3>
                    </div>
                    <div class="col-lg-6 text-end">
                      <a href="add.php" class="btn btn-success btn-sm">+ New Rule</a>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Rule Name</th>
                          <th>Trigger</th>
                          <th>Action</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['rule_name']) ?></td>
                            <td><?= $row['trigger_type'] ?> (<?= $row['trigger_value'] ?>)</td>
                            <td><?= $row['action_type'] ?></td>
                            <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
                            <td>
                              <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-warning">Edit</a>
                              <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete rule?')">Delete</a>
                            </td>
                          </tr>
                        <?php endwhile; ?>
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
    <script>
      $(function () {
        $('#leadsTable').DataTable();
      });
    </script>
  </body>
  <!--end::Body-->
</html>
