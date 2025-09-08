<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$page_title = "CRM Dashboard";

// === Score Distribution ===
$score_buckets = [
    '0–20' => [0, 20],
    '21–40' => [21, 40],
    '41–60' => [41, 60],
    '61–80' => [61, 80],
    '81–100' => [81, 100],
];

$score_data = [];
foreach ($score_buckets as $label => [$min, $max]) {
    $result = $conn->query("
        SELECT COUNT(*) AS count FROM crm_lead_insights i
        JOIN crm_contacts c ON i.contact_id = c.id
        WHERE i.score BETWEEN $min AND $max
        AND c.company_id = {$_SESSION['company_id']} AND c.deleted_at IS NULL
    ");
    $row = $result->fetch_assoc();
    $score_data[$label] = (int)$row['count'];
}

// === Sentiment Breakdown ===
$sentiments = ['positive', 'neutral', 'negative'];
$sentiment_data = [];

foreach ($sentiments as $s) {
    $res = $conn->query("
        SELECT COUNT(*) AS count FROM crm_lead_insights i
        JOIN crm_contacts c ON i.contact_id = c.id
        WHERE i.sentiment = '$s' 
        AND c.company_id = {$_SESSION['company_id']} AND c.deleted_at IS NULL
    ");
    $r = $res->fetch_assoc();
    $sentiment_data[$s] = (int)$r['count'];
}

// === Top 5 Leads ===
$top_leads = $conn->query("
    SELECT c.id, c.first_name, c.last_name, i.score
    FROM crm_lead_insights i
    JOIN crm_contacts c ON i.contact_id = c.id
    WHERE c.company_id = {$_SESSION['company_id']} AND c.deleted_at IS NULL
    ORDER BY i.score DESC LIMIT 5
");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Contacts</title>
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
            <section class="content-header mt-3">
              <h1>CRM Dashboard <small>Lead Intelligence</small></h1>
            </section>

            <section class="content">
              <div class="row mt-5">
                <!-- Lead Score Distribution -->
                <div class="col-md-6">
                  <div class="card card-primary">
                    <div class="card-header with-border"><h3 class="card-title">Lead Score Distribution</h3></div>
                    <div class="card-body">
                      <canvas id="scoreChart" height="160"></canvas>
                    </div>
                  </div>
                </div>

                <!-- Sentiment Breakdown -->
                <div class="col-md-6">
                  <div class="card card-success">
                    <div class="card-header with-border"><h3 class="card-title">Lead Sentiment Breakdown</h3></div>
                    <div class="card-body">
                      <canvas id="sentimentChart" height="160"></canvas>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Top Leads -->
              <div class="row mt-5">
                <div class="col-md-6">
                  <div class="card card-warning">
                    <div class="card-header with-border"><h3 class="card-title">Top 5 Leads by Score</h3></div>
                    <div class="card-body table-responsive">
                      <table class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Score</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while($lead = $top_leads->fetch_assoc()): ?>
                            <tr>
                              <td><a href="../contacts/view.php?id=<?= $lead['id'] ?>">
                                <?= htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) ?>
                              </a></td>
                              <td><span class="badge bg-blue"><?= $lead['score'] ?></span></td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
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
