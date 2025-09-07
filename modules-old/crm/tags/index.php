<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$module = $_GET['module'] ?? '';
$allowed_modules = ['contact', 'company', 'deal', ''];
if (!in_array($module, $allowed_modules)) {
    $module = '';
}

$query = "SELECT * FROM crm_tags WHERE company_id = ?";
$params = [$_SESSION['company_id']];
$types = 'i';

if ($module) {
    $query .= " AND module = ?";
    $params[] = $module;
    $types .= 's';
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "Tags";
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Tags</title>
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
              <h1>Tags <small><?= $module ? ucfirst($module) : 'All' ?></small></h1>
              <a href="add.php?module=<?= $module ?>" class="btn btn-sm btn-primary">Add Tag</a>
            </section>

            <section class="content">
              <div class="box">
                <div class="box-body table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Tag</th>
                        <th>Module</th>
                        <th>Color</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($tag = $result->fetch_assoc()): ?>
                        <tr>
                          <td><?= htmlspecialchars($tag['name']) ?></td>
                          <td><?= ucfirst($tag['module']) ?></td>
                          <td><span class="badge" style="background:<?= $tag['color'] ?>"><?= $tag['color'] ?></span></td>
                          <td>
                            <a href="edit.php?id=<?= $tag['id'] ?>" class="btn btn-xs btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $tag['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this tag?')">Delete</a>
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
