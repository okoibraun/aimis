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

$contact_id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT c.full_name, i.*
    FROM crm_contacts c
    LEFT JOIN crm_lead_insights i ON c.id = i.contact_id
    WHERE c.id = ?
");
$stmt->bind_param("i", $contact_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AIMIS | CRM - AI Insights</title>
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
              <div class="card-header">
                <h4>AI Insight for <?= htmlspecialchars($data['full_name']) ?></h4>
                <div class="card-tools">
                  <a href="../contacts/view.php?id=<?= $contact_id ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Contact
                  </a>
                </div>
              </div>
              <div class="card">
                <div class="card-body">
                  <p><strong>Score:</strong> <?= $data['score'] ?> / 100</p>
                  <p><strong>Reason:</strong> <?= $data['score_reason'] ?></p>
                  <p><strong>Sentiment:</strong>
                    <span class="label label-<?= $data['sentiment'] === 'positive' ? 'success' : ($data['sentiment'] === 'negative' ? 'danger' : 'default') ?>">
                      <?= ucfirst($data['sentiment'] ?? '') ?>
                    </span>
                  </p>
                  <p><strong>Summary:</strong> <?= $data['sentiment_summary'] ?></p>
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
