<?php
require_once '../../../config/db.php';
require_once '../../../functions/auth_functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

$id = intval($_GET['id']);
$company_id = get_current_company_id();

$stmt = $conn->prepare("SELECT * FROM crm_segments WHERE id=? AND company_id=?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Segment not found.");
}

$segment = $result->fetch_assoc();
$filters = json_decode($segment['filters'], true);
$target_table = $segment['target_type'] === 'contact' ? 'crm_contacts' : 'crm_companies';

$query = "SELECT * FROM $target_table WHERE company_id = ?";
$params = [$company_id];
$types = 'i';

if (!empty($filters['tag'])) {
  $query .= " AND tags LIKE ?";
  $params[] = '%' . $filters['tag'] . '%';
  $types .= 's';
}

if (!empty($filters['status'])) {
  $query .= " AND status = ?";
  $params[] = $filters['status'];
  $types .= 's';
}

if (!empty($filters['location'])) {
  $query .= " AND location LIKE ?";
  $params[] = '%' . $filters['location'] . '%';
  $types .= 's';
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$results = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - Segments</title>
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
            <section class="content">
              <div class="card">
                  <div class="card-header">
                      <h3 class="card-title">Segment: <?= htmlspecialchars($segment['segment_name']) ?></h3>
                  </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Tags</th>
                        <th>Location</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $results->fetch_assoc()): ?>
                        <tr>
                          <td><?= htmlspecialchars($row['name']) ?></td>
                          <td><?= htmlspecialchars($row['status']) ?></td>
                          <td><?= htmlspecialchars($row['tags']) ?></td>
                          <td><?= htmlspecialchars($row['location']) ?></td>
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
