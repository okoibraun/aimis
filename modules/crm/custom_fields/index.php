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

// Get Super Roles
$roles = super_roles();

if (!in_array($_SESSION['role'], $roles) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$page_title = "Manage Custom Fields";

$module = $_GET['module'] ?? 'contact';
$allowed_modules = ['contact', 'company', 'deal'];

if (!in_array($module, $allowed_modules)) {
    $module = 'contact';
}

$stmt = $conn->prepare("SELECT * FROM crm_custom_field_definitions WHERE module = ? AND company_id = ?");
$stmt->bind_param('si', $module, $_SESSION['company_id']);
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Custom Fields</title>
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
          

          <!-- Main Content -->
          <div class="content-wrapper">
            <section class="content-header">
              <h1>Custom Fields <small><?= ucfirst($module) ?> Module</small></h1>
            </section>

            <section class="content">
              <div class="box">
                <div class="box-header with-border">
                  <a href="add.php?module=<?= $module ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Add Field</a>
                  <div class="btn-group pull-right">
                    <a href="?module=contact" class="btn btn-default <?= $module == 'contact' ? 'active' : '' ?>">Contacts</a>
                    <a href="?module=company" class="btn btn-default <?= $module == 'company' ? 'active' : '' ?>">Companies</a>
                    <a href="?module=deal" class="btn btn-default <?= $module == 'deal' ? 'active' : '' ?>">Deals</a>
                  </div>
                </div>

                <div class="box-body table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Field Name</th>
                        <th>Type</th>
                        <th>Options</th>
                        <th>Created</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['name']) ?></td>
                          <td><?= htmlspecialchars($row['field_type']) ?></td>
                          <td>
                            <?php
                              if ($row['field_type'] === 'select') {
                                  echo nl2br(htmlspecialchars($row['options']));
                              } else {
                                  echo '-';
                              }
                            ?>
                          </td>
                          <td><?= $row['created_at'] ?></td>
                          <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this custom field?')"><i class="fa fa-trash"></i></a>
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
    <!-- ChartJS -->
    <script src="../../../assets/adminlte/bower_components/chart.js/Chart.min.js"></script>
    <script>
    const scoreCtx = document.getElementById('scoreChart').getContext('2d');
    const sentimentCtx = document.getElementById('sentimentChart').getContext('2d');

    // Score Chart
    new Chart(scoreCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_keys($score_data)) ?>,
        datasets: [{
          label: 'Leads',
          data: <?= json_encode(array_values($score_data)) ?>,
          backgroundColor: 'rgba(60,141,188,0.9)'
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } }
      }
    });

    // Sentiment Chart
    new Chart(sentimentCtx, {
      type: 'pie',
      data: {
        labels: ['Positive', 'Neutral', 'Negative'],
        datasets: [{
          data: <?= json_encode(array_values($sentiment_data)) ?>,
          backgroundColor: ['#00a65a', '#f39c12', '#dd4b39']
        }]
      }
    });
    </script>
  </body>
  <!--end::Body-->
</html>
