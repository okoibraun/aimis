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
$user_permissions = get_user_permissions($user_id);

if (!in_array($_SESSION['role'], super_roles()) && !in_array($page, $user_permissions)) {
    die("You are not authorised to access/perform this page/action <a href='javascript:history.back(1);'>Go Back</a>");
    exit;
}

$company_id = get_current_company_id();
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM crm_activities WHERE id = ? AND company_id = ?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Activity not found.");
$activity = $result->fetch_assoc();
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
            <section class="content-header">
              <h1>Activity: <?= htmlspecialchars($activity['subject']) ?></h1>
              <a href="edit.php?id=<?= $activity['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="./" class="btn btn-default btn-sm">Back</a>
            </section>

            <section class="content">
              <div class="box">
                <div class="box-body">
                  <p><strong>Type:</strong> <?= ucfirst($activity['type']) ?></p>
                  <p><strong>Due Date:</strong> <?= $activity['due_date'] ?></p>
                  <p><strong>Related To:</strong> <?= ucfirst($activity['related_type']) ?> #<?= $activity['related_id'] ?></p>
                  <p><strong>Status:</strong> <?= ucfirst($activity['status']) ?></p>
                  <p><strong>Assigned To:</strong> User #<?= $activity['assigned_to'] ?></p>
                  <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($activity['description'])) ?></p>
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
