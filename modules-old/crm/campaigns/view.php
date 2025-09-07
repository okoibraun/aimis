<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$campaign_id = intval($_GET['id']);
$company_id = get_current_company_id();

$stmt = $conn->prepare("SELECT * FROM crm_campaigns WHERE id=? AND company_id=?");
$stmt->bind_param("ii", $campaign_id, $company_id);
$stmt->execute();
$campaign = $stmt->get_result()->fetch_assoc();

$logs = $conn->query("SELECT * FROM crm_campaign_logs WHERE campaign_id = $campaign_id");
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Campaigns</title>
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
            <section class="content-header">
              <h1>Campaign: <?= htmlspecialchars($campaign['campaign_name']) ?></h1>
              <small>Status: <b><?= ucfirst($campaign['status']) ?></b></small>
            </section>

            <section class="content">
              <div class="box">
                <div class="box-header">Logs</div>
                <div class="box-body">
                  <table class="table table-bordered">
                    <thead><tr>
                      <th>Target ID</th>
                      <th>Medium</th>
                      <th>Status</th>
                      <th>Sent</th>
                    </tr></thead>
                    <tbody>
                      <?php while ($log = $logs->fetch_assoc()): ?>
                        <tr>
                          <td><?= $log['target_id'] ?></td>
                          <td><?= ucfirst($log['medium']) ?></td>
                          <td><?= ucfirst($log['status']) ?></td>
                          <td><?= $log['sent_at'] ?? '—' ?></td>
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
