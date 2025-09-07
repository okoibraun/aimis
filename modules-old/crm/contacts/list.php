<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$company_id = $_SESSION['company_id'];

// Filters
$score_min = isset($_GET['score_min']) ? intval($_GET['score_min']) : 0;
$sentiment = isset($_GET['sentiment']) ? $_GET['sentiment'] : '';

$where = "c.company_id = $company_id AND c.deleted_at IS NULL";
if ($score_min > 0) $where .= " AND i.score >= $score_min";
if (in_array($sentiment, ['positive', 'neutral', 'negative'])) {
    $where .= " AND i.sentiment = '" . $conn->real_escape_string($sentiment) . "'";
}

// Fetch contacts with AI insights
$sql = "
    SELECT c.*, i.score, i.sentiment
    FROM crm_contacts c
    LEFT JOIN crm_lead_insights i ON c.id = i.contact_id
    WHERE $where
    ORDER BY c.created_at DESC
";

$contacts = $conn->query($sql);
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
          

          <div class="content-wrapper">
            <section class="content-header mt-3 mb-3">
              <div class="row">
                <div class="col-lg-6">
                  <h2 class="">CRM Contacts</h2>
                </div>
                <div class="col-lg-6 text-end">
                  <ol class="breadcrumb float-end">
                    <li class="breadcrumb-item"><a href="../../../index.php"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="#">CRM</a></li>
                    <li class="breadcrumb-item active">Contacts</li>
                  </ol>
                </div>
              </div>
            </section>

            <section class="content">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-lg-6">
                      <h3 class="card-title">Contacts</h3>
                    </div>
                    <div class="col-lg-6 text-end">
                      <a href="add.php" class="btn btn-success btn-sm">+ Add Contact</a>
                      <a href="../ai/analyze.php" class="btn btn-info btn-sm">AI Analyze</a>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-3"></div>
                    <div class="col-lg-6">
                      <!-- Filter Form -->
                      <form method="get" class="form-inline align-items-center">
                        <div class="row align-items-center">
                          <div class="col-auto">
                            <label>AI Score ≥</label>
                          </div>
                          <div class="col-auto">
                            <input type="number" name="score_min" class="form-control" value="<?= htmlspecialchars($score_min) ?>" style="width: 80px; margin: 0 10px;">
                          </div>
                          <div class="col-auto">
                            <label>Sentiment</label>
                          </div>
                          <div class="col-auto">
                            <select name="sentiment" class="form-control" style="margin: 0 10px;">
                              <option value="">All</option>
                              <option value="positive" <?= $sentiment === 'positive' ? 'selected' : '' ?>>Positive</option>
                              <option value="neutral" <?= $sentiment === 'neutral' ? 'selected' : '' ?>>Neutral</option>
                              <option value="negative" <?= $sentiment === 'negative' ? 'selected' : '' ?>>Negative</option>
                            </select>
                          </div>
                          <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                          </div>
                        </div>
                      </form>
                    </div>
                    <div class="col-lg-3">&nbsp;</div>
                  </div>

                  <!-- Contacts Table -->
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Lead Score</th>
                        <th>Sentiment</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $contacts->fetch_assoc()): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['full_name']) ?></td>
                          <td><?= htmlspecialchars($row['email']) ?></td>
                          <td><?= htmlspecialchars($row['phone']) ?></td>
                          <td>
                            <?php if ($row['score'] !== null): ?>
                              <span class="badge bg-blue"><?= $row['score'] ?></span>
                            <?php else: ?>
                              <span class="text-muted">N/A</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php
                              $s = $row['sentiment'];
                              if ($s === 'positive') echo '<span class="label label-success">Positive</span>';
                              elseif ($s === 'negative') echo '<span class="label label-danger">Negative</span>';
                              elseif ($s === 'neutral') echo '<span class="label label-default">Neutral</span>';
                              else echo '<span class="text-muted">N/A</span>';
                            ?>
                          </td>
                          <td>
                            <a href="view.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a>
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
