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
$target_table = $segment['target_type'] === 'lead' ? 'sales_customers' : 'sales_customers';
$target_type = $segment['target_type'] === 'lead' ? 'lead' : 'customer';

$query = "SELECT * FROM $target_table WHERE company_id = ? AND customer_type = '$target_type'";
$params = [$company_id];
$types = 'i';

// if (!empty($filters['tag'])) {
//   $query .= " AND tags LIKE ?";
//   $params[] = '%' . $filters['tag'] . '%';
//   $types .= 's';
// }

// if (!empty($filters['status'])) {
//   $query .= " AND status = ?";
//   $params[] = $filters['status'];
//   $types .= 's';
// }

// if (!empty($filters['location'])) {
//   $query .= " AND location LIKE ?";
//   $params[] = '%' . $filters['location'] . '%';
//   $types .= 's';
// }

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
                      <h3 class="card-title">Segment: <?= $segment['segment_name'] ?></h3>
                  </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <!-- <th>Tags</th> -->
                        <th>Location</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $results->fetch_assoc()): ?>
                        <tr>
                          <td><?= $row['name'] ?></td>
                          <td><?= $row['status'] ?></td>
                          <!-- <td><?= $row['tags'] ?></td> -->
                          <td><?= $row['city'] ?></td>
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
  </body>
  <!--end::Body-->
</html>
